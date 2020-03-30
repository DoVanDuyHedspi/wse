<?php

namespace App;

use App\Permission;
use App\Role;

trait HasPermissionsTrait
{

  public function roles()
  {
    return $this->belongsToMany(Role::class, 'users_roles');
  }

  public function permissions()
  {
    return $this->belongsToMany(Permission::class, 'users_permissions');
  }

  // kiem tra user co 1 hoac 1 so role nhat dinh
  public function hasRole(...$roles)
  {
    foreach ($roles as $role) {
      if ($this->roles->contains('slug', $role)) {
        return true;
      }
    }
    return false;
  }

  // kiem tra user co permission do khong
  public function hasPermission($permission)
  {
    $condition1 = (bool) $this->permissions->where('slug', $permission->slug)->count();
    $roles = $this->roles;
    $condition2 = false;
    foreach($roles as $role) {
      if($role->permissions->where('slug', $permission->slug)->count()) {
        $condition2 = true;
      }
    }
    return $condition1 || $condition2;
  }

  protected function getAllPermissions($permissions)
  {
    return Permission::whereIn('slug', $permissions)->get();
  }

  public function givePermissionsTo(...$permissions)
  {
    $permissions = $this->getAllPermissions($permissions);
    if ($permissions === null) {
      return $this;
    }
    $this->permissions()->saveMany($permissions);
    return $this;
  }

  public function deletePermissions(...$permissions)
  {
    $permissions = $this->getAllPermissions($permissions);
    $this->permissions()->detach($permissions);
    return $this;
  }
}
