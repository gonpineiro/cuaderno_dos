<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class JazzService
{
    protected $connection;

    public function __construct($connectionName = 'jazz')
    {
        $this->connection = DB::connection($connectionName);
    }

    public function query($sql, $bindings = [])
    {
        return $this->connection->select($sql, $bindings);
    }
}
