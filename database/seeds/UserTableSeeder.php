<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;
use App\User;
use App\Group;
use App\Position;
use App\EmployeeType;
use App\SalaryRank;
use App\Branch;

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
    $group = Group::all()->first();
    $branch = Branch::all()->first();
    $salary = SalaryRank::all()->first();
    $employee_type = EmployeeType::all()->first();
    $position = Position::all()->first();

    $employee = new User();
    $employee->name = 'Nhan vien binh thuong';
    $employee->email = 'employee_1@wsm.com';
    $employee->password = bcrypt('123456');
    $employee->group_id = $group->id;
    $employee->branch_id = $branch->id;
    $employee->salary_rank_id = $salary->id;
    $employee->position_id = $position->id;
    $employee->employee_type_id = $employee_type->id;
    $employee->save(); 
    $employee->roles()->attach($employee_role);
    
    $direct_manager = new User();
    $direct_manager->name = 'Quan ly truc tiep';
    $direct_manager->email = 'direct_manager_1@wsm.com';
    $direct_manager->password = bcrypt('123456');
    $direct_manager->group_id = $group->id;
    $direct_manager->branch_id = $branch->id;
    $direct_manager->salary_rank_id = $salary->id;
    $direct_manager->position_id = $position->id;
    $direct_manager->employee_type_id = $employee_type->id;
    $direct_manager->save(); 
    $direct_manager->roles()->attach($direct_manager_role);
    $direct_manager->permissions()->attach($manager_view_perm);

    $group_manager = new User();
    $group_manager->name = 'Truong group';
    $group_manager->email = 'group_manager_1@wsm.com';
    $group_manager->password = bcrypt('123456');
    $group_manager->group_id = $group->id;
    $group_manager->branch_id = $branch->id;
    $group_manager->salary_rank_id = $salary->id;
    $group_manager->position_id = $position->id;
    $group_manager->employee_type_id = $employee_type->id;
    $group_manager->save(); 
    $group_manager->roles()->attach($group_manager_role);
    $group_manager->permissions()->attach($manager_view_perm);

    $manager = new User();
    $manager->name = 'Giam doc';
    $manager->email = 'manager_1@wsm.com';
    $manager->password = bcrypt('123456');
    $manager->group_id = $group->id;
    $manager->branch_id = $branch->id;
    $manager->salary_rank_id = $salary->id;
    $manager->position_id = $position->id;
    $manager->employee_type_id = $employee_type->id;
    $manager->save(); 
    $manager->roles()->attach($manager_role);
    $manager->permissions()->attach($manager_perm); 
  }
}
