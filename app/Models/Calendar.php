<?php

namespace App\Models;

use App\Models\Availability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'external_url'];

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }
}
