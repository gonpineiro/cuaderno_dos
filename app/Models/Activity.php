<?php

namespace App\Models;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    protected $hidden = [
        'subject_id',
        'causer_id',
        'subject_type',
        'causer_type',
        /* 'created_at', */
        'updated_at',
        'properties',
    ];
}
