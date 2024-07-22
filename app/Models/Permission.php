<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;

class Permission extends \Spatie\Permission\Models\Permission
{
    use LogsActivity;

    protected $hidden = ['pivot', 'guard_name',  'created_at', 'updated_at'];
}
