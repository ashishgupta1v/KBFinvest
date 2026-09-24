<?php

namespace App\Domain\Leads\Models;

use Illuminate\Database\Eloquent\Model;

class LeadInquiry extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'service_type',
        'details',
        'consent_given',
        'ip_address',
    ];
}
