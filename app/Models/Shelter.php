<?php
// app/Models/Shelter.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Shelter extends Model {
    protected $fillable = ['name','location','latitude','longitude','capacity','current_occupancy','contact_person','contact_no','status'];
    public function getOccupancyPercent(): int {
        if ($this->capacity == 0) return 0;
        return (int) min(100, round($this->current_occupancy / $this->capacity * 100));
    }
}
