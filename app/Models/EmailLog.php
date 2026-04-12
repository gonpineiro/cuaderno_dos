<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'type',
        'to',
        'subject',
        'from',
        'entity_type',
        'entity_id',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public static function hasBeenSent(string $type, string $entityType, int $entityId): bool
    {
        return self::where('type', $type)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('status', 'sent')
            ->exists();
    }
}