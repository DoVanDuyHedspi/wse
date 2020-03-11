<?php

use Illuminate\Database\Seeder;
use App\Permission;
use App\Role;

class RoleTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $employee_role = new Role();
    $employee_role->slug = 'employee';
    $employee_role->name = 'Nhân viên';
    $employee_role->save();

    $direct_manager_role = new Role();
    $direct_manager_role->slug = 'direct-manager';
    $direct_manager_role->name = 'Quản lý trực tiếp';
    $direct_manager_role->save();

    $group_manager_role = new Role();
    $group_manager_role->slug = 'group-manager';
    $group_manager_role->name = 'Trưởng group';
    $group_manager_role->save();

    $manager_role = new Role();
    $manager_role->slug = 'manager';
    $manager_role->name = 'Giám đốc';
    $manager_role->save();
  }
}
