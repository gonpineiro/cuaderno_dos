<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    protected $subjectMap = [
        'App\\Models\\Product' => [
            'method' => 'audit_products',
            'resource' => ProductResource::class
        ],
    ];

    public function toArray($request)
    {
        if (isset($this->subjectMap[$this->subject_type])) {
            $method = $this->subjectMap[$this->subject_type]['method'];
            return $this->$method($request);
        }

        return parent::toArray($request);
    }

    private function audit_products($request)
    {
        //$resource = $this->subjectMap[$this->subject_type]['resource'];

        $product =  $this->loadDeletedSubject();

        $data = [
            'id' => $this->id,
            'causer' => $this->causer->name,
            'log_name' => $this->log_name,

            'subject' => $product ? [
                'id' => $product->id,
                'code' => $product->code,
            ] : null,

            'created_at' => $this->created_at,
        ];

        return $data;
    }

    private function loadDeletedSubject()
    {
        return $this->subject_type::withTrashed()->find($this->subject_id);
    }
}
