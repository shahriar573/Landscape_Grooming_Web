<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model
{
    protected $fillable = ['name', 'description', 'price', 'duration', 'image_path'];

    /**
     * Bookings for this service
     */
    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class);
    }
}
