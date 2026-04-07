<?php
// app/Models/AuditLog.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id','action','module','record_id','ip_address','details','created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
