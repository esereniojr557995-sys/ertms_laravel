<?php
// database/migrations/2024_01_01_000002_create_incidents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['fire','flood','earthquake','medical','rescue','hazmat','wind','other']);
            $table->enum('severity', ['low','moderate','high','critical'])->default('low');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['open','active','contained','closed'])->default('open');
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('commander_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_reported')->useCurrent();
            $table->timestamp('date_closed')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('incidents'); }
};
