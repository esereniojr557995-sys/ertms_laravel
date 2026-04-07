<?php
// app/Models/TrainingRecord.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrainingRecord extends Model {
    protected $fillable = ['user_id','training_program_id','status','score','date_completed','certificate_issued'];
    protected $casts = ['date_completed'=>'datetime','certificate_issued'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function program() { return $this->belongsTo(TrainingProgram::class, 'training_program_id'); }
}
