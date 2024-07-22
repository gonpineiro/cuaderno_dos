<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;

class Role extends \Spatie\Permission\Models\Role
{
    use LogsActivity;

    protected $hidden = ['pivot', 'guard_name',  'created_at', 'updated_at'];
}
