<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->nullable()->constrained('member_plans')->onDelete('set null');
            $table->string('member_code', 20);
            $table->string('name',100);
            $table->string('email',100)->nullable();
            $table->date('join_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 15)->nullable();
            $table->enum('status', ['active','inactive','suspend', 'expired'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
