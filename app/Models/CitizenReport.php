<?php
// app/Models/CitizenReport.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CitizenReport extends Model {
    protected $fillable = ['user_id','title','description','location','latitude','longitude','type','status','photo'];
    public function user() { return $this->belongsTo(User::class); }
}
