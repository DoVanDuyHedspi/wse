<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(PermissionTableSeeder::class);
    $this->call(RoleTableSeeder::class);
    $this->call(EmployeeTypesTableSeeder::class);
    $this->call(SalaryRanksTableSeeder::class);
    $this->call(PositionsTableSeeder::class);
    $this->call(BranchesTableSeeder::class);
    $this->call(GroupsTableSeeder::class);
    $this->call(UserTableSeeder::class);
  }
}
