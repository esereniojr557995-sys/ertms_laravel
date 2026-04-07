<?php
// app/Http/Controllers/Responder/ResponderController.php
namespace App\Http\Controllers\Responder;

use App\Http\Controllers\Controller;
use App\Models\{Incident, Task, Resource, Alert, Message, TrainingProgram, TrainingRecord, User, Shelter};
use Illuminate\Http\Request;

class ResponderController extends Controller
{
    public function dashboard()
    {
        $myTasks       = Task::where('assigned_to', auth()->id())->whereIn('status',['pending','in_progress'])->count();
        $activeAlerts  = Alert::where('status','sent')->latest()->take(3)->get();
        $recentIncidents = Incident::whereIn('status',['open','active'])->latest()->take(5)->get();
        $myTraining    = TrainingRecord::where('user_id', auth()->id())->with('program')->latest()->take(3)->get();

        return view('responder.dashboard', compact('myTasks','activeAlerts','recentIncidents','myTraining'));
    }

    // ── Incidents (Read Only) ──────────────────────────────────────────────
    public function incidents(Request $request)
    {
        $incidents = Incident::with('reporter')
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->latest()->paginate(15);
        return view('responder.incidents.index', compact('incidents'));
    }

    public function showIncident(Incident $incident)
    {
        $incident->load(['tasks.assignee','reporter','commander']);
        return view('responder.incidents.show', compact('incident'));
    }

    // ── My Tasks (R/W own tasks) ───────────────────────────────────────────
    public function tasks(Request $request)
    {
        $tasks = Task::where('assigned_to', auth()->id())
            ->with('incident')
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->latest()->paginate(15);
        return view('responder.tasks.index', compact('tasks'));
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        // Responders can only update tasks assigned to them
        abort_unless($task->assigned_to === auth()->id(), 403);
        $data = $request->validate(['status' => 'required|in:in_progress,completed']);
        if ($data['status'] === 'completed') $data['completed_at'] = now();
        $task->update($data);
        return redirect()->back()->with('success','Task status updated.');
    }

    // ── Resources (Read) ───────────────────────────────────────────────────
    public function resources()
    {
        $resources = Resource::latest()->paginate(15);
        return view('responder.resources.index', compact('resources'));
    }

    // ── Alerts (Read) ──────────────────────────────────────────────────────
    public function alerts()
    {
        $alerts = Alert::with('sender')->where('status','sent')->latest()->paginate(15);
        return view('responder.alerts.index', compact('alerts'));
    }

    // ── Communications (R/W) ──────────────────────────────────────────────
    public function comms()
    {
        $users    = User::where('id','!=',auth()->id())->whereIn('role',['admin','commander','responder'])->get();
        $messages = Message::where('sender_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->with(['sender','receiver'])
            ->latest()->take(50)->get();
        return view('responder.comms.index', compact('users','messages'));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'content'     => 'required|string|max:1000',
            'channel'     => 'required|in:internal,radio',
        ]);
        $data['sender_id'] = auth()->id();
        Message::create($data);
        return redirect()->back()->with('success','Message sent.');
    }

    // ── Mapping (Read) ─────────────────────────────────────────────────────
    public function mapping()
    {
        $incidents = Incident::whereIn('status',['open','active'])->whereNotNull('latitude')->get();
        $shelters  = Shelter::where('status','open')->get();
        return view('responder.mapping', compact('incidents','shelters'));
    }

    // ── Training (R/W own records) ─────────────────────────────────────────
    public function training()
    {
        $programs = TrainingProgram::with('trainer')->whereIn('status',['upcoming','ongoing'])->get();
        $myRecords = TrainingRecord::where('user_id', auth()->id())->with('program')->get();
        return view('responder.training.index', compact('programs','myRecords'));
    }

    public function enrollTraining(Request $request)
    {
        $data = $request->validate(['training_program_id' => 'required|exists:training_programs,id']);
        $exists = TrainingRecord::where('user_id', auth()->id())
            ->where('training_program_id', $data['training_program_id'])->exists();
        if ($exists) {
            return redirect()->back()->with('error','Already enrolled.');
        }
        TrainingRecord::create([
            'user_id'             => auth()->id(),
            'training_program_id' => $data['training_program_id'],
            'status'              => 'enrolled',
        ]);
        return redirect()->back()->with('success','Enrolled successfully.');
    }
}
