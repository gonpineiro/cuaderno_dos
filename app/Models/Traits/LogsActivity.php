<?php

namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getDescriptionForEvent(string $eventName): string
    {
        $arr = ['updated' => 'actualizado', 'created' => 'creado', 'deleted' => 'eliminado'];
        $rta = $arr[$eventName];

        return "Este modelo ha sido {$rta}";
    }
}
