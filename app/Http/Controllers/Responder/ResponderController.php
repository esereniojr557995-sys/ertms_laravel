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
    public function comms(Request $request)
    {
        $users = User::where('id','!=',auth()->id())
            ->whereIn('role',['admin','commander','responder'])
            ->orderBy('name')->get();

        $withUserId = $request->input('with');
        $withUser   = $withUserId ? User::find($withUserId) : null;

        if ($withUser) {
            $messages = Message::with(['sender','receiver'])
                ->where(function($q) use ($withUserId) {
                    $q->where(function($q2) use ($withUserId) {
                        $q2->where('sender_id', auth()->id())->where('receiver_id', $withUserId);
                    })->orWhere(function($q2) use ($withUserId) {
                        $q2->where('sender_id', $withUserId)->where('receiver_id', auth()->id());
                    });
                })->oldest()->take(100)->get();
            Message::where('sender_id',$withUserId)->where('receiver_id',auth()->id())->where('is_read',false)->update(['is_read'=>true]);
        } else {
            $messages = Message::with(['sender','receiver'])
                ->where(function($q) {
                    $q->where('sender_id', auth()->id())
                      ->orWhere('receiver_id', auth()->id())
                      ->orWhereNull('receiver_id');
                })->oldest()->take(100)->get();
        }

        $conversations = [];
        $myId = auth()->id();
        $sent = Message::where('sender_id',$myId)->whereNotNull('receiver_id')->pluck('receiver_id');
        $recv = Message::where('receiver_id',$myId)->pluck('sender_id');
        foreach ($sent->merge($recv)->unique()->values() as $uid) {
            $u = User::find($uid);
            if (!$u) continue;
            $unread = Message::where('sender_id',$uid)->where('receiver_id',$myId)->where('is_read',false)->count();
            $last   = Message::where(function($q) use ($uid,$myId){
                $q->where(function($q2) use ($uid,$myId){$q2->where('sender_id',$myId)->where('receiver_id',$uid);})->orWhere(function($q2) use ($uid,$myId){$q2->where('sender_id',$uid)->where('receiver_id',$myId);});
            })->latest()->first();
            $conversations[] = ['user'=>$u,'unread'=>$unread,'last_msg'=>$last?->content,'last_at'=>$last?->created_at->diffForHumans()];
        }

        return view('responder.comms.index', compact('users','messages','withUser','conversations'));
    }

    public function fetchMessages(Request $request)
    {
        $since      = $request->input('since', 0);
        $withUserId = $request->input('with');
        if ($withUserId) {
            $messages = Message::with(['sender','receiver'])->where('id','>',$since)->where(function($q) use ($withUserId){
                $q->where(function($q2) use ($withUserId){$q2->where('sender_id',auth()->id())->where('receiver_id',$withUserId);})->orWhere(function($q2) use ($withUserId){$q2->where('sender_id',$withUserId)->where('receiver_id',auth()->id());});
            })->oldest()->get();
        } else {
            $messages = Message::with(['sender','receiver'])->where('id','>',$since)->where(function($q){
                $q->where('sender_id',auth()->id())->orWhere('receiver_id',auth()->id())->orWhereNull('receiver_id');
            })->oldest()->get();
        }
        return response()->json($messages->map(fn($m)=>[
            'id'=>$m->id,'sender_id'=>$m->sender_id,'sender_name'=>$m->sender->name,
            'receiver_id'=>$m->receiver_id,'receiver_name'=>$m->receiver?->name,
            'channel'=>$m->channel,'content'=>$m->content,
            'is_mine'=>$m->sender_id===auth()->id(),
            'time'=>$m->created_at->diffForHumans(),'time_full'=>$m->created_at->format('M d, H:i'),
        ]));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'content'     => 'required|string|max:1000',
            'channel'     => 'required|in:internal,radio',
        ]);
        $data['sender_id'] = auth()->id();
        $msg = Message::create($data);
        if ($request->expectsJson() || $request->ajax()) {
            $msg->load('sender','receiver');
            return response()->json(['id'=>$msg->id,'sender_id'=>$msg->sender_id,'sender_name'=>$msg->sender->name,'receiver_id'=>$msg->receiver_id,'receiver_name'=>$msg->receiver?->name,'channel'=>$msg->channel,'content'=>$msg->content,'is_mine'=>true,'time'=>$msg->created_at->diffForHumans(),'time_full'=>$msg->created_at->format('M d, H:i')]);
        }
        $with = isset($data['receiver_id']) && $data['receiver_id'] ? '?with='.$data['receiver_id'] : '';
        return redirect(route('responder.comms').$with);
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
