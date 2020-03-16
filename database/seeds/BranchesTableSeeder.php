<?php

use Illuminate\Database\Seeder;
use App\Branch;

class BranchesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $branch1 = new Branch();
    $branch1->name = "Tp Hồ Chí Minh";
    $branch1->description = "Quận 1 tp Hồ Chí Minh";
    $branch1->save();
  }
}
