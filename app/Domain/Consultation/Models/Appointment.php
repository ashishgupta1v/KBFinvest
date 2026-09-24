<?php

namespace App\Domain\Consultation\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'mode',
        'topic',
        'date',
        'time',
        'notes',
        'status',
    ];
}
