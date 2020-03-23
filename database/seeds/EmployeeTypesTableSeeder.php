<?php

use Illuminate\Database\Seeder;
use App\EmployeeType;

class EmployeeTypesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $type1 = new EmployeeType();
    $type1->name = "Intern";
    $type1->save();

    $type2 = new EmployeeType();
    $type2->name = "Partime";
    $type2->save();

    $type3 = new EmployeeType();
    $type3->name = "Fulltime";
    $type3->save();
  }
}
