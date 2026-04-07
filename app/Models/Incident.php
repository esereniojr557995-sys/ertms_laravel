<?php
// app/Models/Incident.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model {
    use SoftDeletes;
    protected $fillable = ['title','type','severity','location','latitude','longitude','description','status','reported_by','commander_id','date_reported','date_closed'];
    protected $casts = ['date_reported' => 'datetime', 'date_closed' => 'datetime'];

    public function reporter() { return $this->belongsTo(User::class, 'reported_by'); }
    public function commander() { return $this->belongsTo(User::class, 'commander_id'); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function patients() { return $this->hasMany(Patient::class); }
    public function alerts() { return $this->hasMany(Alert::class); }

    public function getSeverityClass(): string {
        return match($this->severity) {
            'low'      => 'severity-low',
            'moderate' => 'severity-moderate',
            'high'     => 'severity-high',
            'critical' => 'severity-critical',
            default    => '',
        };
    }
    public function getStatusClass(): string {
        return match($this->status) {
            'open'      => 'status-open',
            'active'    => 'status-active',
            'contained' => 'status-contained',
            'closed'    => 'status-closed',
            default     => '',
        };
    }
    public function getTypeIcon(): string {
        return match($this->type) {
            'fire'       => 'flame',
            'flood'      => 'droplets',
            'earthquake' => 'activity',
            'medical'    => 'heart-pulse',
            'rescue'     => 'life-buoy',
            'hazmat'     => 'biohazard',
            'wind'       => 'wind',
            default      => 'alert-triangle',
        };
    }
}
