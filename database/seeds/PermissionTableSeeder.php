<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $createUsers = new Permission();
    $createUsers->slug = 'create-users';
    $createUsers->name = 'Create Users';
    $createUsers->save();

    $deleteUsers = new Permission();
    $deleteUsers->slug = 'delete-users';
    $deleteUsers->name = 'Delete Users';
    $deleteUsers->save();

    $viewGroupUsers = new Permission();
    $viewGroupUsers->slug = 'view-group-users';
    $viewGroupUsers->name = 'View Users Of Group';
    $viewGroupUsers->save();
  }
}
