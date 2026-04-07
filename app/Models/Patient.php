<?php
// app/Models/Patient.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Patient extends Model {
    protected $fillable = ['name','age','gender','triage_level','location_found','incident_id','assigned_medic_id','hospital_name','status','notes'];
    public function incident() { return $this->belongsTo(Incident::class); }
    public function medic() { return $this->belongsTo(User::class, 'assigned_medic_id'); }
    public function getTriageClass(): string {
        return match($this->triage_level) {
            'immediate'  => 'triage-immediate',
            'delayed'    => 'triage-delayed',
            'minor'      => 'triage-minor',
            'expectant'  => 'triage-expectant',
            'deceased'   => 'triage-deceased',
            default      => '',
        };
    }
}
