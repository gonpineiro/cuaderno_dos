<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $permissions = Permission::with('roles')->get();
        $roles = Role::with('permissions')->get();

        /* $permissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        })->map(function ($group) {
            return $group->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'description' => $per    ission->description,
                ];
            });
        }); */

        $users = User::with('permissions', 'roles.permissions')->get();

        return sendResponse([
            'permissions' => $permissions,
            'roles' => $roles,
            'users' => $users
        ]);
    }

    public function change_user_role(Request $request)
    {
        $user = User::find($request->user_id);
        $role = Role::find($request->role_id);

        if ($request->action === 'add_user_role') {
            $user->assignRole($role);

            $permissions = $role->permissions();
            $permissions->each(function ($permission) use ($user) {
                $user->revokePermissionTo($permission);
            });
        } else if ($request->action === 'remove_user_role') {
            $user->removeRole($role);
        }

        return $this->index($request);
    }

    public function change_user_permissions(Request $request)
    {
        $user = User::find($request->user_id);
        $permissions = $request->permissions;

        foreach ($permissions as $permission) {
            if ($permission['checked']) {
                $user->givePermissionTo($permission['name']);
            } else {
                $user->revokePermissionTo($permission['name']);
            }
        }

        return $this->index($request);
    }

    public function save_element(Request $request)
    {
        if ($request->model === 'role') {
            $role = $request->element;
            if ($request->edit) {
                $role = Role::find($role['id'])->update($request->element);
            } else {
                $role['guard_name'] = 'web';
                Role::create($role);
            }

            return $this->index($request);
        }


        if ($request->model === 'permission') {
            $permission = $request->element;
            if ($request->edit) {
                $role = Permission::find($permission['id'])->update($request->element);
            } else {
                $permission['guard_name'] = 'web';
                Permission::create($permission);
            }
            return $this->index($request);
        }

        return $this->index($request);
    }

    public function change_role_permission(Request $request)
    {
        $role = Role::find($request->role_id);

        $permissions = $request->permissions;

        foreach ($permissions as $permission) {
            if ($permission['checked']) {
                $role->givePermissionTo($permission['name']);
            } else {
                $role->revokePermissionTo($permission['name']);
            }
        }

        return $this->index($request);
    }
}
