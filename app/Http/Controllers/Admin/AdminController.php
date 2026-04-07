<?php
// app/Http/Controllers/Admin/AdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Incident, Task, Resource, Alert, Patient, Shelter, AuditLog, TrainingProgram};
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use LogsActivity;

    public function dashboard()
    {
        $stats = [
            'active_incidents' => Incident::whereIn('status', ['open','active'])->count(),
            'critical_incidents' => Incident::where('severity','critical')->whereIn('status',['open','active'])->count(),
            'total_users' => User::count(),
            'active_responders' => User::where('role','responder')->where('status','on_duty')->count(),
            'pending_tasks' => Task::where('status','pending')->count(),
            'low_resources' => Resource::whereRaw('quantity <= min_threshold')->count(),
            'patients_today' => Patient::whereDate('created_at', today())->count(),
            'open_shelters' => Shelter::where('status','open')->count(),
        ];

        $recentIncidents = Incident::with(['reporter','commander'])
            ->latest()->take(5)->get();
        $recentAlerts = Alert::with('sender')->latest()->take(5)->get();
        $recentLogs = AuditLog::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats','recentIncidents','recentAlerts','recentLogs'));
    }

    // ── Users ──────────────────────────────────────────────────────────────
    public function users(Request $request)
    {
        $users = User::when($request->search, fn($q) => $q->where('name','like',"%{$request->search}%")->orWhere('email','like',"%{$request->search}%"))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createUser() { return view('admin.users.create'); }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users',
            'password'       => 'required|min:8|confirmed',
            'role'           => 'required|in:admin,commander,responder,citizen',
            'phone'          => 'nullable|string|max:20',
            'unit'           => 'nullable|string|max:100',
            'rank'           => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:100',
            'status'         => 'required|in:active,inactive,on_duty,off_duty,unavailable',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $this->logActivity('CREATE','Users',$user->id,"Created user {$user->name}");
        return redirect()->route('admin.users')->with('success','User created successfully.');
    }

    public function editUser(User $user) { return view('admin.users.edit', compact('user')); }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => "required|email|unique:users,email,{$user->id}",
            'role'           => 'required|in:admin,commander,responder,citizen',
            'phone'          => 'nullable|string|max:20',
            'unit'           => 'nullable|string|max:100',
            'rank'           => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:100',
            'status'         => 'required|in:active,inactive,on_duty,off_duty,unavailable',
        ]);
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        $this->logActivity('UPDATE','Users',$user->id,"Updated user {$user->name}");
        return redirect()->route('admin.users')->with('success','User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        $this->logActivity('DELETE','Users',$user->id,"Deleted user {$user->name}");
        $user->delete();
        return redirect()->route('admin.users')->with('success','User deleted.');
    }

    // ── Incidents ──────────────────────────────────────────────────────────
    public function incidents(Request $request)
    {
        $incidents = Incident::with(['reporter','commander'])
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->when($request->severity, fn($q) => $q->where('severity',$request->severity))
            ->latest()->paginate(15);
        return view('admin.incidents.index', compact('incidents'));
    }

    public function createIncident()
    {
        $commanders = User::where('role','commander')->get();
        return view('admin.incidents.create', compact('commanders'));
    }

    public function storeIncident(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'type'         => 'required|in:fire,flood,earthquake,medical,rescue,hazmat,wind,other',
            'severity'     => 'required|in:low,moderate,high,critical',
            'location'     => 'required|string|max:200',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'description'  => 'nullable|string',
            'status'       => 'required|in:open,active,contained,closed',
            'commander_id' => 'nullable|exists:users,id',
        ]);
        $data['reported_by'] = auth()->id();
        $incident = Incident::create($data);
        $this->logActivity('CREATE','Incidents',$incident->id,"Created incident: {$incident->title}");
        return redirect()->route('admin.incidents')->with('success','Incident created.');
    }

    public function editIncident(Incident $incident)
    {
        $commanders = User::where('role','commander')->get();
        return view('admin.incidents.edit', compact('incident','commanders'));
    }

    public function updateIncident(Request $request, Incident $incident)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'type'         => 'required',
            'severity'     => 'required|in:low,moderate,high,critical',
            'location'     => 'required|string|max:200',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'description'  => 'nullable|string',
            'status'       => 'required|in:open,active,contained,closed',
            'commander_id' => 'nullable|exists:users,id',
        ]);
        if ($data['status'] === 'closed' && !$incident->date_closed) {
            $data['date_closed'] = now();
        }
        $incident->update($data);
        $this->logActivity('UPDATE','Incidents',$incident->id,"Updated incident: {$incident->title}");
        return redirect()->route('admin.incidents')->with('success','Incident updated.');
    }

    public function destroyIncident(Incident $incident)
    {
        $this->logActivity('DELETE','Incidents',$incident->id,"Deleted incident: {$incident->title}");
        $incident->delete();
        return redirect()->route('admin.incidents')->with('success','Incident deleted.');
    }

    // ── Resources ──────────────────────────────────────────────────────────
    public function resources(Request $request)
    {
        $resources = Resource::when($request->type, fn($q) => $q->where('type',$request->type))
            ->latest()->paginate(15);
        $lowStock = Resource::whereRaw('quantity <= min_threshold')->count();
        return view('admin.resources.index', compact('resources','lowStock'));
    }

    public function storeResource(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|in:equipment,vehicle,medical_supply,personnel,other',
            'quantity'      => 'required|integer|min:0',
            'min_threshold' => 'required|integer|min:0',
            'unit'          => 'required|string|max:20',
            'location'      => 'nullable|string|max:100',
            'status'        => 'required|in:available,in_use,maintenance,depleted',
        ]);
        $resource = Resource::create($data);
        $this->logActivity('CREATE','Resources',$resource->id,"Added resource: {$resource->name}");
        return redirect()->route('admin.resources')->with('success','Resource added.');
    }

    public function updateResource(Request $request, Resource $resource)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'quantity'      => 'required|integer|min:0',
            'min_threshold' => 'required|integer|min:0',
            'unit'          => 'required|string|max:20',
            'location'      => 'nullable|string|max:100',
            'status'        => 'required|in:available,in_use,maintenance,depleted',
        ]);
        $resource->update($data);
        $this->logActivity('UPDATE','Resources',$resource->id,"Updated resource: {$resource->name}");
        return redirect()->route('admin.resources')->with('success','Resource updated.');
    }

    public function destroyResource(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources')->with('success','Resource deleted.');
    }

    // ── Alerts ─────────────────────────────────────────────────────────────
    public function alerts()
    {
        $alerts = Alert::with('sender','incident')->latest()->paginate(15);
        $incidents = Incident::whereIn('status',['open','active'])->get();
        return view('admin.alerts.index', compact('alerts','incidents'));
    }

    public function storeAlert(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:150',
            'message'         => 'required|string',
            'severity'        => 'required|in:info,warning,high,critical',
            'type'            => 'required|in:evacuation,weather,incident,public,system',
            'target_audience' => 'required|array',
            'incident_id'     => 'nullable|exists:incidents,id',
        ]);
        $data['sent_by'] = auth()->id();
        $data['status']  = 'sent';
        $alert = Alert::create($data);
        $this->logActivity('CREATE','Alerts',$alert->id,"Sent alert: {$alert->title}");
        return redirect()->route('admin.alerts')->with('success','Alert sent.');
    }

    public function destroyAlert(Alert $alert)
    {
        $alert->delete();
        return redirect()->route('admin.alerts')->with('success','Alert deleted.');
    }

    // ── Medical / Patients ─────────────────────────────────────────────────
    public function patients(Request $request)
    {
        $patients  = Patient::with(['incident','medic'])
            ->when($request->triage, fn($q) => $q->where('triage_level',$request->triage))
            ->latest()->paginate(15);
        $incidents = Incident::whereIn('status',['open','active'])->get();
        $medics    = User::whereIn('role',['admin','commander'])->get();
        return view('admin.patients.index', compact('patients','incidents','medics'));
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'age'               => 'nullable|integer|min:0|max:120',
            'gender'            => 'required|in:male,female,unknown',
            'triage_level'      => 'required|in:immediate,delayed,minor,expectant,deceased',
            'location_found'    => 'nullable|string|max:200',
            'incident_id'       => 'required|exists:incidents,id',
            'assigned_medic_id' => 'nullable|exists:users,id',
            'hospital_name'     => 'nullable|string|max:100',
            'status'            => 'required|in:on_scene,transported,admitted,discharged,deceased',
            'notes'             => 'nullable|string',
        ]);
        $patient = Patient::create($data);
        $this->logActivity('CREATE','Medical',$patient->id,"Admitted patient: {$patient->name}");
        return redirect()->route('admin.patients')->with('success','Patient record created.');
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'triage_level'      => 'required|in:immediate,delayed,minor,expectant,deceased',
            'assigned_medic_id' => 'nullable|exists:users,id',
            'hospital_name'     => 'nullable|string|max:100',
            'status'            => 'required|in:on_scene,transported,admitted,discharged,deceased',
            'notes'             => 'nullable|string',
        ]);
        $patient->update($data);
        $this->logActivity('UPDATE','Medical',$patient->id,"Updated patient: {$patient->name}");
        return redirect()->route('admin.patients')->with('success','Patient updated.');
    }

    // ── Training ───────────────────────────────────────────────────────────
    public function training()
    {
        $programs = TrainingProgram::with('trainer')->latest()->paginate(15);
        $trainers = User::whereIn('role',['admin','commander'])->get();
        return view('admin.training.index', compact('programs','trainers'));
    }

    public function storeTraining(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:150',
            'description'      => 'nullable|string',
            'type'             => 'required|in:basic,advanced,certification,drill,online',
            'date_scheduled'   => 'nullable|date',
            'location'         => 'nullable|string|max:150',
            'trainer_id'       => 'nullable|exists:users,id',
            'max_participants' => 'required|integer|min:1',
            'status'           => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);
        TrainingProgram::create($data);
        return redirect()->route('admin.training')->with('success','Training program created.');
    }

    public function updateTraining(Request $request, TrainingProgram $program)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'status'         => 'required|in:upcoming,ongoing,completed,cancelled',
            'date_scheduled' => 'nullable|date',
            'location'       => 'nullable|string|max:150',
        ]);
        $program->update($data);
        return redirect()->route('admin.training')->with('success','Training updated.');
    }

    // ── Reports / Analytics ────────────────────────────────────────────────
    public function reports()
    {
        $incidentsByType     = Incident::selectRaw('type, count(*) as total')->groupBy('type')->get();
        $incidentsBySeverity = Incident::selectRaw('severity, count(*) as total')->groupBy('severity')->get();
        $incidentsByStatus   = Incident::selectRaw('status, count(*) as total')->groupBy('status')->get();
        $tasksByStatus       = Task::selectRaw('status, count(*) as total')->groupBy('status')->get();
        $resourceByType      = Resource::selectRaw('type, sum(quantity) as total')->groupBy('type')->get();
        $patientsByTriage    = Patient::selectRaw('triage_level, count(*) as total')->groupBy('triage_level')->get();
        $monthly             = Incident::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))->groupBy('month')->orderBy('month')->get();

        return view('admin.reports', compact(
            'incidentsByType','incidentsBySeverity','incidentsByStatus',
            'tasksByStatus','resourceByType','patientsByTriage','monthly'
        ));
    }

    // ── Audit Logs ─────────────────────────────────────────────────────────
    public function auditLogs(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->module, fn($q) => $q->where('module',$request->module))
            ->latest('created_at')->paginate(25);
        return view('admin.audit_logs', compact('logs'));
    }

    // ── Shelters (Mapping) ─────────────────────────────────────────────────
    public function shelters()
    {
        $shelters  = Shelter::paginate(15);
        return view('admin.shelters.index', compact('shelters'));
    }

    public function storeShelter(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'location'          => 'required|string|max:200',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'capacity'          => 'required|integer|min:0',
            'current_occupancy' => 'required|integer|min:0',
            'contact_person'    => 'nullable|string|max:100',
            'contact_no'        => 'nullable|string|max:20',
            'status'            => 'required|in:open,full,closed',
        ]);
        Shelter::create($data);
        return redirect()->route('admin.shelters')->with('success','Shelter added.');
    }

    public function updateShelter(Request $request, Shelter $shelter)
    {
        $data = $request->validate([
            'current_occupancy' => 'required|integer|min:0',
            'status'            => 'required|in:open,full,closed',
        ]);
        $shelter->update($data);
        return redirect()->route('admin.shelters')->with('success','Shelter updated.');
    }

    // ── Settings ───────────────────────────────────────────────────────────
    public function settings() { return view('admin.settings'); }
}
