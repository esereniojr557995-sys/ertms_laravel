<?php
// app/Models/Alert.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Alert extends Model {
    protected $fillable = ['title','message','severity','type','target_audience','status','sent_by','incident_id'];
    protected $casts = ['target_audience' => 'array'];
    public function sender() { return $this->belongsTo(User::class, 'sent_by'); }
    public function incident() { return $this->belongsTo(Incident::class); }
    public function getSeverityClass(): string {
        return match($this->severity) {
            'info'     => 'alert-info',
            'warning'  => 'alert-warning',
            'high'     => 'alert-high',
            'critical' => 'alert-critical',
            default    => '',
        };
    }
}
