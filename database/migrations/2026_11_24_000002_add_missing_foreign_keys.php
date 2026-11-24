<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The columns were unsigned integers, so they are widened to match the
     * bigint primary keys they reference before the constraints are added.
     */
    public function up(): void
    {
        Schema::table('book_authors', function (Blueprint $table) {
            $table->unsignedBigInteger('book_id')->change();
            $table->unsignedBigInteger('author_id')->change();
        });
        Schema::table('book_authors', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->change();
            $table->unsignedBigInteger('book_id')->change();
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });

        // Audit history must outlive the user, so a hard delete is refused.
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropForeignKeys('audit_logs', ['user_id']);
        $this->dropForeignKeys('reviews', ['member_id', 'book_id']);
        $this->dropForeignKeys('book_authors', ['book_id', 'author_id']);
    }

    private function dropForeignKeys(string $tableName, array $columns): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                $table->dropForeign([$column]);
            }
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
            foreach ($columns as $column) {
                // MySQL keeps the index it created for the constraint.
                $index = "{$tableName}_{$column}_foreign";
                if (Schema::hasIndex($tableName, $index)) {
                    $table->dropIndex($index);
                }

                $table->unsignedInteger($column)->change();
            }
        });
    }
};
