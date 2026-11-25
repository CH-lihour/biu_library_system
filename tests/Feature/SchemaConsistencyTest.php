<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SchemaConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_remember_me_stores_a_remember_token(): void
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'Remember Me',
            'email' => 'remember@example.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'Librarian')->value('id'),
        ]);

        $this->post(route('store'), [
            'email' => 'remember@example.com',
            'password' => 'password',
            'remember' => 'on',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertNotNull(User::find($id)->remember_token);
    }

    public function test_user_factory_creates_users(): void
    {
        $this->assertTrue(User::factory()->create()->fresh()->is_verified);
        $this->assertFalse(User::factory()->unverified()->create()->fresh()->is_verified);
    }

    public function test_book_copy_has_borrow_transactions(): void
    {
        $book = Book::create(['isbn' => '9780000000001', 'title' => 'Test Book', 'language' => 'English']);
        $copy = BookCopy::create(['book_id' => $book->id, 'barcode' => 'BC-1', 'status' => 'borrowed']);
        $member = Member::create(['member_code' => 'M-1', 'name' => 'Member', 'status' => 'active']);
        $borrow = BorrowTransaction::create([
            'member_id' => $member->id,
            'book_copy_id' => $copy->id,
            'borrow_date' => '2026-01-01',
            'due_date' => '2026-01-15',
            'status' => 'active',
        ]);

        $this->assertTrue($copy->borrows()->first()->is($borrow));
    }

    #[DataProvider('orphanRows')]
    public function test_foreign_keys_reject_orphan_rows(string $table, array $row): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/foreign key constraint/i');

        DB::table($table)->insert($row);
    }

    public static function orphanRows(): array
    {
        return [
            'book_authors' => ['book_authors', ['book_id' => 999, 'author_id' => 999]],
            'reviews' => ['reviews', ['member_id' => 999, 'book_id' => 999, 'rating' => 5, 'review_text' => 'x']],
            'audit_logs' => ['audit_logs', [
                'table_name' => 'books', 'action' => 'create', 'user_id' => 999, 'record_id' => 1,
                'old_values' => '{}', 'new_values' => '{}', 'ip_address' => '127.0.0.1',
            ]],
        ];
    }
}
