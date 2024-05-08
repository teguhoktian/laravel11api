<?php

namespace App\Http\Controllers;

use App\APIResponseBuilder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
    public function index(): JsonResponse
    {
        $roles = Role::select(['id', 'name', 'guard_name'])->orderBy('name', 'ASC')->get();
        return APIResponseBuilder::success($roles);
    }

    /**
     * Store New Role
     *
     * @param Request $request->name
     * @param Request $request->guard_name
     * 
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|unique:roles']);
        $role = Role::create($request->only('name', 'guard_name'));
        $role->syncPermissions($request->only('permissions'));

        return APIResponseBuilder::success($role, __('Role successfuly saved.'));
    }

    public function getRole(Role $role): JsonResponse
    {
        $permissions = Permission::select(['id', 'name', 'guard_name'])->orderBy('id', 'ASC')->get();

        return APIResponseBuilder::success([
            'role' => $role,
            'role_permissions' => $role->permissions()->pluck('name'),
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        ($role->name == "Admin") ? $role->syncPermissions(Permission::all()) : $role->syncPermissions($request->only('permissions'));

        return APIResponseBuilder::success($role, __('Role successfuly updated.'));
    }

    public function destroy(Role $role)
    {
        $roleTmp = $role;
        $role->delete();
        return APIResponseBuilder::success($roleTmp, __('Role successfuly deleted.'));
    }
}
