<?php
// app/Models/Resource.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Resource extends Model {
    protected $fillable = ['name','type','quantity','min_threshold','unit','location','status','assigned_incident_id'];
    public function incident() { return $this->belongsTo(Incident::class, 'assigned_incident_id'); }
    public function isLow(): bool { return $this->quantity <= $this->min_threshold; }
}
