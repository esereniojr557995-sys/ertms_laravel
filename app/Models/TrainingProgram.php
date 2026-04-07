<?php
// app/Models/TrainingProgram.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrainingProgram extends Model {
    protected $fillable = ['title','description','type','date_scheduled','location','trainer_id','max_participants','status'];
    protected $casts = ['date_scheduled' => 'datetime'];
    public function trainer() { return $this->belongsTo(User::class, 'trainer_id'); }
    public function records() { return $this->hasMany(TrainingRecord::class); }
}
