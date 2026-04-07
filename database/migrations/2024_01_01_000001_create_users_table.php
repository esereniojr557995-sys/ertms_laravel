<?php
// database/migrations/2024_01_01_000001_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'commander', 'responder', 'citizen'])->default('citizen');
            $table->string('phone')->nullable();
            $table->string('unit')->nullable();
            $table->string('rank')->nullable();
            $table->string('specialization')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_duty', 'off_duty', 'unavailable'])->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
