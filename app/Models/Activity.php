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

    public static $log_names = ['user', 'error'];

    /** Controlamos los subject_types que queremos operar */
    public static $subject_types = [
        [
            'label' => 'Actas',
            'value' => 'App\Models\ActasFotoMulta'
        ],
    ];

    public static function get_log_names()
    {
        return self::$log_names;
    }

    public static function get_subject_types()
    {
        return self::$subject_types;
    }
}
