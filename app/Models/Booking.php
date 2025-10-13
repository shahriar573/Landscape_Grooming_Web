<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model
{
    protected $fillable = ['service_id', 'customer_id', 'staff_id', 'scheduled_at', 'price', 'status', 'notes'];
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
    public function service() { return $this->belongsTo(Service::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function staff() { return $this->belongsTo(User::class, 'staff_id'); }
}
