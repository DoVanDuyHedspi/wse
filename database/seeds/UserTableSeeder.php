<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;
use App\User;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $direct_manager_role = Role::where('slug', 'direct-manager')->first();
    $group_manager_role = Role::where('slug', 'group-manager')->first();
    $manager_role = Role::where('slug', 'manager')->first();
    $employee_role = Role::where('slug', 'employee')->first();
    $manager_view_perm = Permission::where('slug', 'view-group-users')->first();
    $manager_perm = Permission::where('slug', 'create-users')->first();

    $employee = new User();
    $employee->name = 'Nhan vien binh thuong';
    $employee->email = 'employee_1@wsm.com';
    $employee->password = bcrypt('123456');
    $employee->save(); 
    $employee->roles()->attach($employee_role);
    
    $direct_manager = new User();
    $direct_manager->name = 'Quan ly truc tiep';
    $direct_manager->email = 'direct_manager_1@wsm.com';
    $direct_manager->password = bcrypt('123456');
    $direct_manager->save(); 
    $direct_manager->roles()->attach($direct_manager_role);
    $direct_manager->permissions()->attach($manager_view_perm);

    $group_manager = new User();
    $group_manager->name = 'Truong group';
    $group_manager->email = 'group_manager_1@wsm.com';
    $group_manager->password = bcrypt('123456');
    $group_manager->save(); 
    $group_manager->roles()->attach($group_manager_role);
    $group_manager->permissions()->attach($manager_view_perm);

    $manager = new User();
    $manager->name = 'Giam doc';
    $manager->email = 'manager_1@wsm.com';
    $manager->password = bcrypt('123456');
    $manager->save(); 
    $manager->roles()->attach($manager_role);
    $manager->permissions()->attach($manager_perm); 
  }
}
