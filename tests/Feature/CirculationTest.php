<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Member;
use App\Models\MemberPlan;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CirculationTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staff = User::factory()->staff()->create();
    }

    private function member(array $card = []): Member
    {
        return User::factory()->member($card)->create()->member;
    }

    private function book(int $copies = 1): Book
    {
        $book = Book::create(['isbn' => fake()->unique()->numerify('978##########'), 'title' => 'Book '.fake()->unique()->word(), 'language' => 'English']);
        for ($i = 0; $i < $copies; $i++) {
            BookCopy::create(['book_id' => $book->id, 'barcode' => fake()->unique()->numerify('BC-######'), 'status' => 'available']);
        }

        return $book;
    }

    private function lend(Member $member, BookCopy ...$copies)
    {
        return $this->actingAs($this->staff)->post(route('borrows.store'), [
            'member_id' => $member->id,
            'book_copy_ids' => array_map(fn ($copy) => $copy->id, $copies),
            'borrow_date' => today()->toDateString(),
            'due_date' => today()->addDays(14)->toDateString(),
        ]);
    }

    public function test_lending_records_the_staff_member_and_returning_closes_the_loan(): void
    {
        $member = $this->member();
        $copy = $this->book()->copies->first();

        $this->lend($member, $copy)->assertRedirect(route('borrows.index'))->assertSessionHas('success');
        $loan = BorrowTransaction::firstOrFail();
        $this->assertSame($this->staff->staff->id, $loan->staff_id);
        $this->assertSame('borrowed', $copy->fresh()->status);

        $this->get(route('borrows.index', ['draw' => 1]), ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('data.0.handle_by', e($this->staff->name));

        $this->post(route('borrows.return', $loan->id))->assertSessionHas('success');
        $this->assertSame('returned', $loan->fresh()->status);
        $this->assertSame('available', $copy->fresh()->status);
    }

    public function test_second_return_does_not_free_a_copy_lent_to_someone_else(): void
    {
        $copy = $this->book()->copies->first();
        $this->lend($this->member(), $copy);
        $first = BorrowTransaction::firstOrFail();
        $this->post(route('borrows.return', $first->id));
        $this->lend($this->member(), $copy)->assertSessionHas('success');

        $this->post(route('borrows.return', $first->id))->assertSessionHas('error');
        $this->assertSame('borrowed', $copy->fresh()->status);
    }

    public function test_lending_is_blocked_by_status_expiry_limit_and_copy_state(): void
    {
        $copy = $this->book()->copies->first();
        $this->lend($this->member(['status' => 'pending']), $copy)->assertSessionHas('error');
        $this->lend($this->member(['status' => 'suspend']), $copy)->assertSessionHas('error');
        $this->lend($this->member(['expiry_date' => today()->subDay()]), $copy)->assertSessionHas('error');
        $this->assertSame('available', $copy->fresh()->status);

        $this->lend($this->member(['expiry_date' => today()]), $copy)->assertSessionHas('success');
        $this->lend($this->member(), $copy)->assertSessionHas('error');

        $six = $this->book(6)->copies->all();
        $this->lend($this->member(), ...$six)->assertSessionHas('error');
        $this->assertSame(1, BorrowTransaction::count());
        $this->assertSame(6, BookCopy::where('status', 'available')->count());
    }

    public function test_member_reserves_and_cannot_duplicate(): void
    {
        $member = $this->member();
        $book = $this->book();
        $this->actingAs($member->user);

        $this->get(route('portal.book', $book->id))->assertOk()->assertSee('Reserve');
        $this->post(route('portal.reserve', $book->id))->assertRedirect(route('portal.reservations'))->assertSessionHas('success');
        $this->post(route('portal.reserve', $book->id))->assertSessionHas('error');
        $this->get(route('portal.reservations'))->assertOk()->assertSee($book->title);
        $this->assertSame(1, Reservation::count());
    }

    public function test_blocked_members_cannot_reserve(): void
    {
        $book = $this->book();
        foreach ([['status' => 'pending'], ['status' => 'suspend'], ['expiry_date' => today()->subDay()]] as $card) {
            $this->actingAs($this->member($card)->user)->post(route('portal.reserve', $book->id))->assertSessionHas('error');
        }
        $this->assertSame(0, Reservation::count());
    }

    public function test_held_copy_goes_to_the_member_who_reserved_it(): void
    {
        [$owner, $other] = [$this->member(), $this->member()];
        $book = $this->book();
        $copy = $book->copies->first();

        $this->actingAs($owner->user)->post(route('portal.reserve', $book->id));
        $reservation = Reservation::firstOrFail();
        $this->actingAs($this->staff)->post(route('reservations.ready', $reservation->id))->assertSessionHas('success');
        $this->assertSame('ready', $reservation->fresh()->displayStatus());

        $this->lend($other, $copy)->assertSessionHas('error');
        $this->lend($owner, $copy)->assertSessionHas('success');
        $this->assertSame('fulfilled', $reservation->fresh()->status);
    }

    public function test_one_copy_cannot_be_held_twice(): void
    {
        $book = $this->book();
        foreach ([$this->member(), $this->member()] as $member) {
            $this->actingAs($member->user)->post(route('portal.reserve', $book->id));
        }
        [$a, $b] = Reservation::orderBy('id')->get()->all();
        $this->actingAs($this->staff)->post(route('reservations.ready', $a->id))->assertSessionHas('success');
        $this->post(route('reservations.ready', $b->id))->assertSessionHas('error');

        // cancelling the first passes the hold on
        $this->post(route('reservations.cancel', $a->id))->assertSessionHas('success');
        $this->assertSame('ready', $b->fresh()->displayStatus());
    }

    public function test_return_holds_the_copy_for_the_first_eligible_member_in_the_queue(): void
    {
        $copy = $this->book()->copies->first();
        $this->lend($this->member(), $copy);
        [$suspended, $first, $second] = [$this->member(), $this->member(), $this->member()];
        foreach ([$suspended, $first, $second] as $member) {
            $this->actingAs($member->user)->post(route('portal.reserve', $copy->book_id));
        }
        $suspended->update(['status' => 'suspend']);

        $this->actingAs($this->staff)->post(route('borrows.return', BorrowTransaction::first()->id))->assertSessionHas('success');
        $this->assertSame(['pending', 'ready', 'pending'], Reservation::orderBy('id')->get()->map->displayStatus()->all());
    }

    public function test_uncollected_hold_expires_and_moves_to_the_next_member(): void
    {
        $book = $this->book();
        foreach ([$this->member(), $this->member()] as $member) {
            $this->actingAs($member->user)->post(route('portal.reserve', $book->id));
        }
        $this->actingAs($this->staff)->post(route('reservations.ready', Reservation::first()->id));

        $this->travel(config('library.hold_days'))->days();
        $this->travel(1)->minutes();
        $this->artisan('library:expire-reservations')->assertSuccessful();

        $this->assertSame(['expired', 'ready'], Reservation::orderBy('id')->get()->map->displayStatus()->all());
    }

    public function test_members_see_and_change_only_their_own_records(): void
    {
        [$mine, $theirs] = [$this->member(), $this->member()];
        $lent = $this->book();
        $reserved = $this->book();
        $this->lend($theirs, $lent->copies->first());
        $this->actingAs($theirs->user)->post(route('portal.reserve', $reserved->id));
        $reservation = Reservation::firstOrFail();

        $this->actingAs($mine->user);
        $this->get(route('portal.loans', ['member' => $theirs->id, 'member_id' => $theirs->id]))->assertOk()->assertDontSee($lent->title);
        $this->get(route('portal.reservations'))->assertOk()->assertDontSee($reserved->title);
        $this->post(route('portal.reservations.cancel', $reservation->id))->assertNotFound();
        $this->assertSame('pending', $reservation->fresh()->status);

        $this->actingAs($theirs->user)->get(route('portal.loans'))->assertSee($lent->title);
        $this->get(route('portal.home'))->assertOk()->assertSee($lent->title);
        $this->post(route('portal.reservations.cancel', $reservation->id))->assertSessionHas('success');
    }

    public function test_members_cannot_use_the_borrow_desk(): void
    {
        $copy = $this->book()->copies->first();
        $member = $this->member();
        $this->lend($member, $copy);
        $this->actingAs($member->user);

        $this->get(route('borrows.index'))->assertForbidden();
        $this->get(route('borrows.create'))->assertForbidden();
        $this->post(route('borrows.store'), [])->assertForbidden();
        $this->post(route('borrows.getBookByBarcode'), ['barcode' => $copy->barcode])->assertForbidden();
        $this->post(route('borrows.return', BorrowTransaction::first()->id))->assertForbidden();
        $this->assertSame('borrowed', $copy->fresh()->status);
    }

    public function test_catalog_search(): void
    {
        $this->actingAs($this->member()->user);
        $a = $this->book();
        $b = $this->book();
        $author = Author::create(['firstname' => 'Zelda', 'lastname' => 'Quark', 'email' => 'z@example.com']);
        $a->authors()->attach($author);

        $this->get(route('portal.catalog'))->assertOk()->assertSee($a->title)->assertSee($b->title);
        $this->get(route('portal.catalog', ['search' => 'Quark']))->assertSee($a->title)->assertDontSee($b->title);
        $this->get(route('portal.catalog', ['search' => '%']))->assertSee('No books match');
        $this->get(route('portal.catalog', ['search' => $b->isbn]))->assertSee($b->title);
    }

    public function test_overdue_fine_uses_the_plan(): void
    {
        $plan = MemberPlan::create(['name' => 'P', 'loan_duration_days' => 14, 'fine_per_day' => 0.5, 'discount_fee' => 0]);
        $member = $this->member(['plan_id' => $plan->id]);
        $this->lend($member, $this->book()->copies->first());
        $this->travel(17)->days();

        $this->actingAs($member->user)->get(route('portal.home'))->assertSee('$1.50')->assertSee('Overdue');
    }
}
