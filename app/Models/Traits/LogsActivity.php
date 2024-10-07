<?php

namespace App\Models\Traits;


trait LogsActivity
{
    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getDescriptionForEvent(string $eventName): string
    {
        $arr = ['updated' => 'actualizado', 'created' => 'creado', 'deleted' => 'eliminado'];
        $rta = $arr[$eventName];

        return "Este modelo ha sido {$rta}";
    }

    /* public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()  // Solo guarda cambios de los atributos que hayan cambiado
            ->setDescriptionForEvent(fn(string $eventName) => $this->getDescriptionForEvent($eventName))
            ->logAll();  // Registra todos los atributos
    } */
}
