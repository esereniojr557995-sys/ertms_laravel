<?php
// database/migrations/2024_01_01_000003_create_tasks_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low','medium','high','critical'])->default('medium');
            $table->enum('status', ['pending','in_progress','completed','cancelled'])->default('pending');
            $table->timestamp('due_datetime')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['equipment','vehicle','medical_supply','personnel','other']);
            $table->integer('quantity')->default(0);
            $table->integer('min_threshold')->default(5);
            $table->string('unit')->default('pcs');
            $table->string('location')->nullable();
            $table->enum('status', ['available','in_use','maintenance','depleted'])->default('available');
            $table->foreignId('assigned_incident_id')->nullable()->constrained('incidents')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('severity', ['info','warning','high','critical'])->default('info');
            $table->enum('type', ['evacuation','weather','incident','public','system'])->default('public');
            $table->json('target_audience')->nullable(); // ['all','responders','citizens']
            $table->enum('status', ['draft','sent','failed'])->default('draft');
            $table->foreignId('sent_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('incident_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->default('internal'); // internal, radio, public
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['basic','advanced','certification','drill','online']);
            $table->timestamp('date_scheduled')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('max_participants')->default(20);
            $table->enum('status', ['upcoming','ongoing','completed','cancelled'])->default('upcoming');
            $table->timestamps();
        });

        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_program_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['enrolled','attended','passed','failed','dropped'])->default('enrolled');
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('date_completed')->nullable();
            $table->boolean('certificate_issued')->default(false);
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age')->nullable();
            $table->enum('gender', ['male','female','unknown'])->default('unknown');
            $table->enum('triage_level', ['immediate','delayed','minor','expectant','deceased'])->default('minor');
            $table->string('location_found')->nullable();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_medic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('hospital_name')->nullable();
            $table->enum('status', ['on_scene','transported','admitted','discharged','deceased'])->default('on_scene');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shelters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('capacity')->default(0);
            $table->integer('current_occupancy')->default(0);
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->enum('status', ['open','full','closed'])->default('open');
            $table->timestamps();
        });

        Schema::create('citizen_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('type', ['fire','flood','accident','medical','hazard','other'])->default('other');
            $table->enum('status', ['pending','acknowledged','resolved','dismissed'])->default('pending');
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module');
            $table->string('record_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('citizen_reports');
        Schema::dropIfExists('shelters');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('training_records');
        Schema::dropIfExists('training_programs');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('tasks');
    }
};
