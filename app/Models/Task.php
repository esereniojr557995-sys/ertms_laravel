<?php
// app/Models/Task.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    protected $fillable = ['incident_id','assigned_to','created_by','title','description','priority','status','due_datetime','completed_at'];
    protected $casts = ['due_datetime'=>'datetime','completed_at'=>'datetime'];
    public function incident() { return $this->belongsTo(Incident::class); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
