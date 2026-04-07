<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Incident;
use App\Models\Task;
use App\Models\Resource;
use App\Models\Alert;
use App\Models\TrainingProgram;
use App\Models\Patient;
use App\Models\Shelter;
use App\Models\CitizenReport;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        $admin = User::create([
            'name'  => 'System Administrator',
            'email' => 'admin@ertms.gov',
            'password' => Hash::make('password'),
            'role'  => 'admin',
            'phone' => '09171234567',
            'status' => 'active',
        ]);

        $commander = User::create([
            'name'  => 'Maj. Ricardo Santos',
            'email' => 'commander@ertms.gov',
            'password' => Hash::make('password'),
            'role'  => 'commander',
            'phone' => '09181234567',
            'rank'  => 'Major',
            'unit'  => 'Alpha Command',
            'status' => 'on_duty',
        ]);

        $responder = User::create([
            'name'  => 'Sgt. Maria Reyes',
            'email' => 'responder@ertms.gov',
            'password' => Hash::make('password'),
            'role'  => 'responder',
            'phone' => '09191234567',
            'rank'  => 'Sergeant',
            'unit'  => 'Alpha Team',
            'specialization' => 'Search & Rescue',
            'status' => 'on_duty',
        ]);

        $citizen = User::create([
            'name'  => 'Juan dela Cruz',
            'email' => 'citizen@ertms.gov',
            'password' => Hash::make('password'),
            'role'  => 'citizen',
            'phone' => '09201234567',
            'status' => 'active',
        ]);

        // Extra responders
        $r2 = User::create(['name' => 'Cpl. Jose Manalo', 'email' => 'r2@ertms.gov', 'password' => Hash::make('password'), 'role' => 'responder', 'unit' => 'Bravo Team', 'rank' => 'Corporal', 'status' => 'on_duty']);
        $r3 = User::create(['name' => 'PFC. Ana Lim', 'email' => 'r3@ertms.gov', 'password' => Hash::make('password'), 'role' => 'responder', 'unit' => 'Alpha Team', 'rank' => 'PFC', 'status' => 'off_duty']);

        // Incidents
        $inc1 = Incident::create([
            'title' => 'Typhoon Bagyo Flooding',
            'type'  => 'flood',
            'severity' => 'high',
            'location' => 'Brgy. Matina, Davao City',
            'latitude'  => 7.0731,
            'longitude' => 125.6128,
            'description' => 'Flash flooding due to typhoon Bagyo. Multiple families displaced.',
            'status' => 'active',
            'reported_by' => $citizen->id,
            'commander_id' => $commander->id,
        ]);

        $inc2 = Incident::create([
            'title' => 'Structure Fire - Poblacion',
            'type'  => 'fire',
            'severity' => 'critical',
            'location' => 'Poblacion, Davao City',
            'latitude'  => 7.0644,
            'longitude' => 125.6094,
            'description' => '3-storey commercial building on fire. Units deployed.',
            'status' => 'contained',
            'reported_by' => $admin->id,
            'commander_id' => $commander->id,
        ]);

        $inc3 = Incident::create([
            'title' => 'Road Accident - SPED Highway',
            'type'  => 'medical',
            'severity' => 'moderate',
            'location' => 'Southern Philippines Medical Center',
            'latitude'  => 7.0777,
            'longitude' => 125.6128,
            'description' => 'Multi-vehicle accident. 3 casualties reported.',
            'status' => 'open',
            'reported_by' => $citizen->id,
            'commander_id' => null,
        ]);

        // Tasks
        Task::create(['incident_id' => $inc1->id, 'assigned_to' => $responder->id, 'created_by' => $commander->id, 'title' => 'Evacuate Sitio Mabuhay residents', 'priority' => 'high', 'status' => 'in_progress', 'due_datetime' => now()->addHours(3)]);
        Task::create(['incident_id' => $inc1->id, 'assigned_to' => $r2->id, 'created_by' => $commander->id, 'title' => 'Set up mobile command post', 'priority' => 'medium', 'status' => 'pending', 'due_datetime' => now()->addHour()]);
        Task::create(['incident_id' => $inc2->id, 'assigned_to' => $responder->id, 'created_by' => $commander->id, 'title' => 'Perimeter control', 'priority' => 'high', 'status' => 'completed', 'completed_at' => now()->subHour()]);
        Task::create(['incident_id' => $inc3->id, 'assigned_to' => null, 'created_by' => $commander->id, 'title' => 'Assess road safety conditions', 'priority' => 'medium', 'status' => 'pending']);

        // Resources
        Resource::create(['name' => 'Rescue Boat', 'type' => 'vehicle', 'quantity' => 3, 'min_threshold' => 1, 'unit' => 'unit', 'location' => 'Station 1', 'status' => 'in_use', 'assigned_incident_id' => $inc1->id]);
        Resource::create(['name' => 'Life Vests', 'type' => 'equipment', 'quantity' => 45, 'min_threshold' => 20, 'unit' => 'pcs', 'location' => 'Warehouse A', 'status' => 'available']);
        Resource::create(['name' => 'First Aid Kits', 'type' => 'medical_supply', 'quantity' => 8, 'min_threshold' => 10, 'unit' => 'kits', 'location' => 'Medical Bay', 'status' => 'available']);
        Resource::create(['name' => 'Fire Truck', 'type' => 'vehicle', 'quantity' => 2, 'min_threshold' => 1, 'unit' => 'unit', 'location' => 'Station 2', 'status' => 'in_use', 'assigned_incident_id' => $inc2->id]);
        Resource::create(['name' => 'Stretchers', 'type' => 'equipment', 'quantity' => 12, 'min_threshold' => 5, 'unit' => 'pcs', 'location' => 'Medical Bay', 'status' => 'available']);
        Resource::create(['name' => 'Emergency Food Packs', 'type' => 'other', 'quantity' => 200, 'min_threshold' => 50, 'unit' => 'packs', 'location' => 'Warehouse B', 'status' => 'available']);

        // Alerts
        Alert::create(['title' => 'Evacuation Order - Matina District', 'message' => 'Mandatory evacuation for all residents in low-lying areas of Matina. Proceed to designated evacuation centers immediately.', 'severity' => 'high', 'type' => 'evacuation', 'target_audience' => json_encode(['citizens','responders']), 'status' => 'sent', 'sent_by' => $admin->id, 'incident_id' => $inc1->id]);
        Alert::create(['title' => 'Weather Advisory: Super Typhoon', 'message' => 'PAGASA issues Typhoon Signal No. 3 for Davao Region. Expect heavy rainfall and strong winds.', 'severity' => 'critical', 'type' => 'weather', 'target_audience' => json_encode(['all']), 'status' => 'sent', 'sent_by' => $admin->id]);
        Alert::create(['title' => 'System Maintenance', 'message' => 'Scheduled maintenance on Sunday 2AM-4AM. Some features may be unavailable.', 'severity' => 'info', 'type' => 'system', 'target_audience' => json_encode(['all']), 'status' => 'sent', 'sent_by' => $admin->id]);

        // Training Programs
        TrainingProgram::create(['title' => 'Basic Life Support & CPR', 'type' => 'certification', 'description' => 'BLS and CPR certification for all field responders.', 'date_scheduled' => now()->addDays(7), 'location' => 'ERTMS Training Hall', 'trainer_id' => $admin->id, 'max_participants' => 30, 'status' => 'upcoming']);
        TrainingProgram::create(['title' => 'Flood Rescue Operations', 'type' => 'drill', 'description' => 'Practical flood rescue simulation exercise.', 'date_scheduled' => now()->addDays(14), 'location' => 'Davao River Training Site', 'trainer_id' => $commander->id, 'max_participants' => 20, 'status' => 'upcoming']);
        TrainingProgram::create(['title' => 'Hazmat First Responder', 'type' => 'advanced', 'description' => 'Handling hazardous materials during emergency response.', 'date_scheduled' => now()->subDays(3), 'location' => 'Training Center B', 'trainer_id' => $admin->id, 'max_participants' => 15, 'status' => 'completed']);

        // Patients
        Patient::create(['name' => 'Pedro Villanueva', 'age' => 45, 'gender' => 'male', 'triage_level' => 'immediate', 'location_found' => 'Matina Low-land Area', 'incident_id' => $inc1->id, 'assigned_medic_id' => $admin->id, 'status' => 'admitted', 'hospital_name' => 'SPMC Davao']);
        Patient::create(['name' => 'Rosa Tan', 'age' => 32, 'gender' => 'female', 'triage_level' => 'delayed', 'location_found' => 'Matina Crossing', 'incident_id' => $inc1->id, 'status' => 'on_scene']);
        Patient::create(['name' => 'Unnamed Male', 'age' => null, 'gender' => 'male', 'triage_level' => 'minor', 'location_found' => 'SPED Highway Km 4', 'incident_id' => $inc3->id, 'status' => 'on_scene']);

        // Shelters
        Shelter::create(['name' => 'Matina Central School Evacuation Center', 'location' => 'Matina, Davao City', 'latitude' => 7.0723, 'longitude' => 125.6134, 'capacity' => 500, 'current_occupancy' => 342, 'contact_person' => 'Brgy. Captain Gomez', 'contact_no' => '09171112222', 'status' => 'open']);
        Shelter::create(['name' => 'Almendras Gym', 'location' => 'Agdao, Davao City', 'latitude' => 7.0841, 'longitude' => 125.6244, 'capacity' => 800, 'current_occupancy' => 800, 'contact_person' => 'Capt. Dela Rosa', 'contact_no' => '09183334444', 'status' => 'full']);
        Shelter::create(['name' => 'Davao City Sports Complex', 'location' => 'Ilustre St., Davao City', 'latitude' => 7.0644, 'longitude' => 125.6014, 'capacity' => 1200, 'current_occupancy' => 120, 'contact_person' => 'Mr. Palma', 'contact_no' => '09205556666', 'status' => 'open']);

        // Citizen Reports
        CitizenReport::create(['user_id' => $citizen->id, 'title' => 'Flooded road near Gaisano Mall', 'description' => 'The road going to Gaisano Mall is completely flooded. Cannot pass through.', 'location' => 'JP Laurel Ave, Davao City', 'latitude' => 7.0674, 'longitude' => 125.6076, 'type' => 'flood', 'status' => 'acknowledged']);
        CitizenReport::create(['user_id' => $citizen->id, 'title' => 'Fallen tree blocking road', 'description' => 'A large tree fell and is blocking C. Bangoy Street.', 'location' => 'C. Bangoy St., Davao City', 'latitude' => 7.0644, 'longitude' => 125.6044, 'type' => 'hazard', 'status' => 'pending']);
    }
}
