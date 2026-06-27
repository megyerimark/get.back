<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;
    protected $fillable = ['calendar_id', 'slot_time', 'is_booked'];
    protected $casts = [
        'slot_time' => 'datetime',
        'is_booked' => 'boolean',
    ];

  
    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

   
    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}
