<?php

namespace App\Models;

use App\Models\Availability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['availability_id', 'guest_name', 'guest_phone'];

    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }
}
