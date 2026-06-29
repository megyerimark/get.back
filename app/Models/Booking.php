<?php

namespace App\Models;

use App\Models\Availability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    //protected $fillable = ['availability_id', 'guest_name', 'guest_email', 'guest_phone', 'is_used'];
protected $fillable = ['availability_id', 'guest_name', 'guest_phone', 'guest_email', 'is_used', 'verification_code'];
    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }
}
