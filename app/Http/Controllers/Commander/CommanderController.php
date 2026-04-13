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
    // ── Lock: completed tasks cannot be modified ──────────────────────
    if ($task->status === 'completed') {
        return redirect()->route('commander.tasks')
            ->with('error', 'Completed tasks cannot be modified.');
    }
       if (in_array($patient->status, ['discharged', 'deceased'])) {
        return redirect()->route('commander.patients')
            ->with('error', 'This patient record is locked and cannot be modified.');
    }
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
            'status'               => 'required|in:available,in_use,maintenance,depleted',
            'assigned_incident_id' => 'nullable|exists:incidents,id',
            'quantity'             => 'required|integer|min:0',
        ]);
        $resource->update($data);
        return redirect()->back()->with('success','Resource updated.');
    }

    // ── Alerts (R/W) ───────────────────────────────────────────────────────
    public function alerts()
    {
        $alerts    = Alert::with('sender','incident')->latest()->paginate(15);
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
    public function comms(Request $request)
    {
        // All users except self, for recipient dropdown
        $users = User::where('id', '!=', auth()->id())
            ->whereIn('role', ['admin','commander','responder'])
            ->orderBy('name')
            ->get();

        // Selected conversation partner (null = group/broadcast view)
        $withUserId = $request->input('with');
        $withUser   = $withUserId ? User::find($withUserId) : null;

        // Build message thread:
        // If viewing a specific user's conversation, show DMs between the two
        // Otherwise show all messages this user can see (sent, received, or broadcast)
        if ($withUser) {
            $messages = Message::with(['sender','receiver'])
                ->where(function($q) use ($withUserId) {
                    $q->where(function($q2) use ($withUserId) {
                        $q2->where('sender_id', auth()->id())
                           ->where('receiver_id', $withUserId);
                    })->orWhere(function($q2) use ($withUserId) {
                        $q2->where('sender_id', $withUserId)
                           ->where('receiver_id', auth()->id());
                    });
                })
                ->oldest()
                ->take(100)
                ->get();
            // Mark received messages as read
            Message::where('sender_id', $withUserId)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            // Group channel: show broadcasts + messages involving this user
            $messages = Message::with(['sender','receiver'])
                ->where(function($q) {
                    $q->where('sender_id', auth()->id())
                      ->orWhere('receiver_id', auth()->id())
                      ->orWhereNull('receiver_id'); // broadcasts
                })
                ->oldest()
                ->take(100)
                ->get();
        }

        // Sidebar: recent conversations with unread counts
        $conversations = $this->getConversations();

        return view('commander.comms.index', compact('users','messages','withUser','conversations'));
    }

    // AJAX endpoint — returns new messages since a given ID
    public function fetchMessages(Request $request)
    {
        $since      = $request->input('since', 0);
        $withUserId = $request->input('with');

        if ($withUserId) {
            $messages = Message::with(['sender','receiver'])
                ->where('id', '>', $since)
                ->where(function($q) use ($withUserId) {
                    $q->where(function($q2) use ($withUserId) {
                        $q2->where('sender_id', auth()->id())->where('receiver_id', $withUserId);
                    })->orWhere(function($q2) use ($withUserId) {
                        $q2->where('sender_id', $withUserId)->where('receiver_id', auth()->id());
                    });
                })
                ->oldest()->get();
        } else {
            $messages = Message::with(['sender','receiver'])
                ->where('id', '>', $since)
                ->where(function($q) {
                    $q->where('sender_id', auth()->id())
                      ->orWhere('receiver_id', auth()->id())
                      ->orWhereNull('receiver_id');
                })
                ->oldest()->get();
        }

        return response()->json($messages->map(function($m) {
            return [
                'id'          => $m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => $m->sender->name,
                'receiver_id' => $m->receiver_id,
                'receiver_name' => $m->receiver?->name,
                'channel'     => $m->channel,
                'content'     => $m->content,
                'is_mine'     => $m->sender_id === auth()->id(),
                'time'        => $m->created_at->diffForHumans(),
                'time_full'   => $m->created_at->format('M d, H:i'),
            ];
        }));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'content'     => 'required|string|max:1000',
            'channel'     => 'required|in:internal,radio,public',
        ]);
        $data['sender_id'] = auth()->id();
        $msg = Message::create($data);

        // Return JSON for AJAX, redirect for regular form
        if ($request->expectsJson() || $request->ajax()) {
            $msg->load('sender','receiver');
            return response()->json([
                'id'           => $msg->id,
                'sender_id'    => $msg->sender_id,
                'sender_name'  => $msg->sender->name,
                'receiver_id'  => $msg->receiver_id,
                'receiver_name'=> $msg->receiver?->name,
                'channel'      => $msg->channel,
                'content'      => $msg->content,
                'is_mine'      => true,
                'time'         => $msg->created_at->diffForHumans(),
                'time_full'    => $msg->created_at->format('M d, H:i'),
            ]);
        }

        $with = $data['receiver_id'] ? '?with='.$data['receiver_id'] : '';
        return redirect()->route('commander.comms').$with;
    }

    private function getConversations(): array
    {
        // Get unique users who have exchanged messages with current user
        $myId = auth()->id();
        $sent = Message::where('sender_id', $myId)->whereNotNull('receiver_id')->pluck('receiver_id');
        $recv = Message::where('receiver_id', $myId)->pluck('sender_id');
        $userIds = $sent->merge($recv)->unique()->values();

        $conversations = [];
        foreach ($userIds as $uid) {
            $user = User::find($uid);
            if (!$user) continue;
            $unread = Message::where('sender_id', $uid)
                ->where('receiver_id', $myId)
                ->where('is_read', false)->count();
            $lastMsg = Message::where(function($q) use ($uid, $myId) {
                    $q->where(function($q2) use ($uid,$myId){
                        $q2->where('sender_id',$myId)->where('receiver_id',$uid);
                    })->orWhere(function($q2) use ($uid,$myId){
                        $q2->where('sender_id',$uid)->where('receiver_id',$myId);
                    });
                })->latest()->first();
            $conversations[] = [
                'user'     => $user,
                'unread'   => $unread,
                'last_msg' => $lastMsg?->content,
                'last_at'  => $lastMsg?->created_at->diffForHumans(),
            ];
        }
        return $conversations;
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
