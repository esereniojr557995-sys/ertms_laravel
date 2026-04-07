<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name','email','password','role','phone','unit',
        'rank','specialization','avatar','status',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    // Role helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isCommander(): bool { return $this->role === 'commander'; }
    public function isResponder(): bool { return $this->role === 'responder'; }
    public function isCitizen(): bool { return $this->role === 'citizen'; }

    public function getRoleLabel(): string {
        return match($this->role) {
            'admin'     => 'System Administrator',
            'commander' => 'Incident Commander',
            'responder' => 'Field Responder',
            'citizen'   => 'Citizen',
            default     => ucfirst($this->role),
        };
    }

    public function getRoleBadgeClass(): string {
        return match($this->role) {
            'admin'     => 'badge-admin',
            'commander' => 'badge-commander',
            'responder' => 'badge-responder',
            'citizen'   => 'badge-citizen',
            default     => 'badge-secondary',
        };
    }

    // Relationships
    public function reportedIncidents() { return $this->hasMany(Incident::class, 'reported_by'); }
    public function commandedIncidents() { return $this->hasMany(Incident::class, 'commander_id'); }
    public function assignedTasks() { return $this->hasMany(Task::class, 'assigned_to'); }
    public function sentMessages() { return $this->hasMany(Message::class, 'sender_id'); }
    public function receivedMessages() { return $this->hasMany(Message::class, 'receiver_id'); }
    public function trainingRecords() { return $this->hasMany(TrainingRecord::class); }
    public function citizenReports() { return $this->hasMany(CitizenReport::class); }
    public function auditLogs() { return $this->hasMany(AuditLog::class); }
}
