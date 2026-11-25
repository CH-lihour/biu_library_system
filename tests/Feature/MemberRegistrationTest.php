<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Member;
use App\Models\MemberPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private array $form = [
        'name' => 'Sok Dara', 'email' => 'Dara@Example.com', 'phone' => '012345678', 'address' => 'Battambang',
        'password' => 'secret-123', 'password_confirmation' => 'secret-123',
    ];

    private function plan(): MemberPlan
    {
        return MemberPlan::create(['name' => 'Student', 'loan_duration_days' => 14, 'fine_per_day' => 0.5, 'discount_fee' => 0]);
    }

    public function test_visitor_registers_and_lands_in_the_member_front_end_as_pending(): void
    {
        $this->get(route('register'))->assertOk();
        $this->post(route('register.store'), $this->form)->assertRedirect(route('portal.home'));

        $user = User::where('email', 'dara@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->role_id);
        $this->assertTrue(Hash::check('secret-123', $user->password));
        $this->assertSame('pending', $user->member->status);
        $this->assertSame('012345678', $user->member->phone);
        $this->assertSame('MBR-'.str_pad((string) $user->member->id, 5, '0', STR_PAD_LEFT), $user->member->member_code);

        $this->get(route('portal.home'))->assertOk()->assertSee('waiting for approval');
        $this->get(route('books.index'))->assertForbidden();
        $this->get(route('dashboard'))->assertRedirect(route('portal.home'));
    }

    public function test_registration_cannot_choose_a_role_or_status(): void
    {
        $this->post(route('register.store'), $this->form + ['role_id' => 1, 'status' => 'active', 'is_verified' => 1]);

        $user = User::where('email', 'dara@example.com')->firstOrFail();
        $this->assertNull($user->role_id);
        $this->assertSame('pending', $user->member->status);
        $this->assertFalse($user->fresh()->is_verified);
    }

    public function test_registration_rules(): void
    {
        $this->post(route('register.store'), ['password' => '1234567', 'password_confirmation' => '1234567'] + $this->form)->assertSessionHasErrors('password');
        $this->post(route('register.store'), ['password_confirmation' => 'other-123'] + $this->form)->assertSessionHasErrors('password');
        User::factory()->create(['email' => 'dara@example.com']);
        $this->post(route('register.store'), $this->form)->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(0, Member::count());
    }

    public function test_staff_approve_a_registration(): void
    {
        $this->post(route('register.store'), $this->form);
        $member = Member::firstOrFail();
        $plan = $this->plan();

        $this->actingAs(User::factory()->staff()->create());
        $this->get(route('members.approval', $member->id))->assertOk()->assertSee('Sok Dara');
        $this->post(route('members.approve', $member->id), [])->assertSessionHasErrors(['plan_id', 'expiry_date']);
        $this->post(route('members.approve', $member->id), ['plan_id' => $plan->id, 'expiry_date' => today()->addYear()->toDateString()])
            ->assertRedirect(route('members.index'));

        $this->assertNull($member->fresh()->blockedReason());
        $this->post(route('members.approve', $member->id), ['plan_id' => $plan->id, 'expiry_date' => today()->addYear()->toDateString()])->assertSessionHas('error');
    }

    public function test_staff_reject_a_registration(): void
    {
        $this->post(route('register.store'), $this->form);
        $member = Member::firstOrFail();
        $this->actingAs(User::factory()->staff()->create())->post(route('members.reject', $member->id))->assertRedirect();
        $this->assertSame('inactive', $member->fresh()->status);
    }

    public function test_registering_with_an_existing_cards_email_gives_nothing_until_staff_link_it(): void
    {
        $card = Member::create(['member_code' => 'MBR-OLD', 'name' => 'Sok Dara', 'email' => 'dara@example.com', 'status' => 'active',
            'plan_id' => $this->plan()->id, 'join_date' => today(), 'expiry_date' => today()->addYear()]);
        $other = Member::create(['member_code' => 'MBR-X', 'name' => 'Other', 'email' => 'other@example.com', 'status' => 'active']);

        $this->post(route('register.store'), $this->form);
        $user = User::where('email', 'dara@example.com')->firstOrFail();
        $pending = $user->member;
        $this->assertNotSame($card->id, $pending->id);
        $this->assertNull($card->fresh()->user_id);

        $this->actingAs(User::factory()->staff()->create());
        $this->post(route('members.approve', $pending->id), ['link_member_id' => $other->id])->assertSessionHas('error');
        $this->post(route('members.approve', $pending->id), ['link_member_id' => $card->id])->assertRedirect(route('members.index'));

        $this->assertSame($user->id, $card->fresh()->user_id);
        $this->assertNull(Member::withTrashed()->find($pending->id));
    }

    public function test_member_updates_own_profile_and_password(): void
    {
        $user = User::factory()->member()->create();
        $this->actingAs($user);

        $this->put(route('portal.profile.update'), ['name' => 'New Name', 'phone' => '098765432', 'status' => 'suspend', 'email' => 'x@example.com'])->assertSessionHas('success');
        $this->assertSame('New Name', $user->fresh()->name);
        $this->assertSame('active', $user->member->fresh()->status);
        $this->assertNotSame('x@example.com', $user->fresh()->email);

        $this->put(route('portal.profile.update'), ['name' => 'A', 'current_password' => 'wrong', 'password' => 'brand-new-1', 'password_confirmation' => 'brand-new-1'])->assertSessionHas('error');
        $this->put(route('portal.profile.update'), ['name' => 'A', 'current_password' => 'password', 'password' => 'brand-new-1', 'password_confirmation' => 'brand-new-1'])->assertSessionHas('success');
        $this->assertTrue(Hash::check('brand-new-1', $user->fresh()->password));
    }

    public function test_login_sends_each_person_to_their_own_front_end(): void
    {
        $member = User::factory()->member()->create();
        $this->get(route('books.index'))->assertRedirect(route('login'));
        $this->post(route('store'), ['email' => $member->email, 'password' => 'password'])->assertRedirect(route('portal.home'));
        $this->get('/')->assertRedirect(route('portal.home'));
        $this->post(route('logout'));

        $book = Book::create(['isbn' => '9780000000001', 'title' => 'T', 'language' => 'English']);
        $this->get(route('portal.book', $book->id))->assertRedirect(route('login'));
        $this->post(route('store'), ['email' => $member->email, 'password' => 'password'])->assertRedirect(route('portal.book', $book->id));
        $this->post(route('logout'));

        $staff = User::factory()->staff()->create();
        $this->post(route('store'), ['email' => $staff->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->get(route('portal.home'))->assertRedirect(route('dashboard'));
    }
}
