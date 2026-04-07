<?php
// app/Http/Controllers/Commander/CommanderController.php
namespace App\Http\Controllers\Commander;

use App\Http\Controllers\Controller;
use App\Models\{Incident, Task, Resource, Alert, Message, Patient, User, Shelter};
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class CommanderController extends Controller
{
    use LogsActivity;

    public function dashboard()
    {
        $myIncidents   = Incident::where('commander_id', auth()->id())->whereIn('status',['open','active'])->count();
        $pendingTasks  = Task::where('status','pending')->count();
        $activeTeam    = User::where('role','responder')->where('status','on_duty')->count();
        $openPatients  = Patient::whereIn('status',['on_scene','transported'])->count();
        $recentIncidents = Incident::with('reporter')->latest()->take(5)->get();
        $recentAlerts    = Alert::latest()->take(4)->get();
        $myTasks         = Task::where('created_by', auth()->id())->where('status','pending')->take(5)->get();

        return view('commander.dashboard', compact(
            'myIncidents','pendingTasks','activeTeam','openPatients',
            'recentIncidents','recentAlerts','myTasks'
        ));
    }

    // ── Incidents ──────────────────────────────────────────────────────────
    public function incidents(Request $request)
    {
        $incidents = Incident::with(['reporter','commander'])
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->when($request->severity, fn($q) => $q->where('severity',$request->severity))
            ->latest()->paginate(15);
        return view('commander.incidents.index', compact('incidents'));
    }

    public function createIncident()
    {
        return view('commander.incidents.create');
    }

    public function storeIncident(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'type'        => 'required',
            'severity'    => 'required|in:low,moderate,high,critical',
            'location'    => 'required|string|max:200',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'description' => 'nullable|string',
            'status'      => 'required|in:open,active,contained,closed',
        ]);
        $data['reported_by']  = auth()->id();
        $data['commander_id'] = auth()->id();
        $incident = Incident::create($data);
        $this->logActivity('CREATE','Incidents',$incident->id,"Created incident: {$incident->title}");
        return redirect()->route('commander.incidents')->with('success','Incident created.');
    }

    public function showIncident(Incident $incident)
    {
        $incident->load(['reporter','commander','tasks.assignee','patients']);
        $responders = User::where('role','responder')->get();
        return view('commander.incidents.show', compact('incident','responders'));
    }

    public function updateIncident(Request $request, Incident $incident)
    {
        $data = $request->validate([
            'status'   => 'required|in:open,active,contained,closed',
            'severity' => 'required|in:low,moderate,high,critical',
        ]);
        if ($data['status'] === 'closed' && !$incident->date_closed) {
            $data['date_closed'] = now();
        }
        $incident->update($data);
        $this->logActivity('UPDATE','Incidents',$incident->id,"Status changed to {$data['status']}");
        return redirect()->back()->with('success','Incident updated.');
    }

    // ── Tasks ──────────────────────────────────────────────────────────────
    public function tasks(Request $request)
    {
        $tasks = Task::with(['incident','assignee'])
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->latest()->paginate(15);
        $incidents  = Incident::whereIn('status',['open','active'])->get();
        $responders = User::where('role','responder')->get();
        return view('commander.tasks.index', compact('tasks','incidents','responders'));
    }

    public function storeTask(Request $request)
    {
        $data = $request->validate([
            'incident_id'  => 'required|exists:incidents,id',
            'assigned_to'  => 'nullable|exists:users,id',
            'title'        => 'required|string|max:150',
            'description'  => 'nullable|string',
            'priority'     => 'required|in:low,medium,high,critical',
            'due_datetime' => 'nullable|date',
        ]);
        $data['created_by'] = auth()->id();
        $data['status']     = 'pending';
        Task::create($data);
        $this->logActivity('CREATE','Tasks',null,"Created task: {$data['title']}");
        return redirect()->route('commander.tasks')->with('success','Task created.');
    }

    public function updateTask(Request $request, Task $task)
    {
        $data = $request->validate([
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        if ($data['status'] === 'completed') $data['completed_at'] = now();
        $task->update($data);
        return redirect()->back()->with('success','Task updated.');
    }

    public function destroyTask(Task $task)
    {
        $task->delete();
        return redirect()->route('commander.tasks')->with('success','Task deleted.');
    }

    // ── Resources (R/W) ────────────────────────────────────────────────────
    public function resources()
    {
        $resources = Resource::latest()->paginate(15);
        $incidents = Incident::whereIn('status',['open','active'])->get();
        return view('commander.resources.index', compact('resources','incidents'));
    }

    public function updateResource(Request $request, Resource $resource)
    {
        $data = $request->validate([
            'status'              => 'required|in:available,in_use,maintenance,depleted',
            'assigned_incident_id'=> 'nullable|exists:incidents,id',
            'quantity'            => 'required|integer|min:0',
        ]);
        $resource->update($data);
        return redirect()->back()->with('success','Resource updated.');
    }

    // ── Alerts (R/W) ───────────────────────────────────────────────────────
    public function alerts()
    {
        $alerts   = Alert::with('sender','incident')->latest()->paginate(15);
        $incidents = Incident::whereIn('status',['open','active'])->get();
        return view('commander.alerts.index', compact('alerts','incidents'));
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
        Alert::create($data);
        return redirect()->route('commander.alerts')->with('success','Alert sent.');
    }

    // ── Communications ─────────────────────────────────────────────────────
    public function comms()
    {
        $users    = User::where('id','!=',auth()->id())->get();
        $messages = Message::where('sender_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->with(['sender','receiver'])
            ->latest()->take(50)->get();
        return view('commander.comms.index', compact('users','messages'));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'content'     => 'required|string|max:1000',
            'channel'     => 'required|in:internal,radio,public',
        ]);
        $data['sender_id'] = auth()->id();
        Message::create($data);
        return redirect()->back()->with('success','Message sent.');
    }

    // ── Medical (R/W) ──────────────────────────────────────────────────────
    public function patients(Request $request)
    {
        $patients  = Patient::with(['incident','medic'])->latest()->paginate(15);
        $incidents = Incident::whereIn('status',['open','active'])->get();
        return view('commander.patients.index', compact('patients','incidents'));
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'age'            => 'nullable|integer',
            'gender'         => 'required|in:male,female,unknown',
            'triage_level'   => 'required|in:immediate,delayed,minor,expectant,deceased',
            'location_found' => 'nullable|string',
            'incident_id'    => 'required|exists:incidents,id',
            'hospital_name'  => 'nullable|string|max:100',
            'status'         => 'required|in:on_scene,transported,admitted,discharged,deceased',
            'notes'          => 'nullable|string',
        ]);
        Patient::create($data);
        return redirect()->route('commander.patients')->with('success','Patient recorded.');
    }

    public function updatePatient(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'triage_level'  => 'required|in:immediate,delayed,minor,expectant,deceased',
            'hospital_name' => 'nullable|string|max:100',
            'status'        => 'required|in:on_scene,transported,admitted,discharged,deceased',
            'notes'         => 'nullable|string',
        ]);
        $patient->update($data);
        return redirect()->back()->with('success','Patient updated.');
    }

    // ── Mapping ────────────────────────────────────────────────────────────
    public function mapping()
    {
        $incidents = Incident::whereIn('status',['open','active'])->whereNotNull('latitude')->get();
        $shelters  = Shelter::all();
        return view('commander.mapping', compact('incidents','shelters'));
    }

    // ── Reports ────────────────────────────────────────────────────────────
    public function reports()
    {
        $incidentsByType     = Incident::selectRaw('type, count(*) as total')->groupBy('type')->get();
        $incidentsBySeverity = Incident::selectRaw('severity, count(*) as total')->groupBy('severity')->get();
        $tasksByStatus       = Task::selectRaw('status, count(*) as total')->groupBy('status')->get();
        $patientsByTriage    = Patient::selectRaw('triage_level, count(*) as total')->groupBy('triage_level')->get();
        return view('commander.reports', compact('incidentsByType','incidentsBySeverity','tasksByStatus','patientsByTriage'));
    }
}


