<?php

use Illuminate\Database\Seeder;
use App\SalaryRank;

class SalaryRanksTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $rank1 = new SalaryRank();
    $rank1->name_rank = "Bậc 1";
    $rank1->basic_salary = "4200000";
    $rank1->save();

    $rank2 = new SalaryRank();
    $rank2->name_rank = "Bậc 2";
    $rank2->basic_salary = "8400000";
    $rank2->save();

    $rank3 = new SalaryRank();
    $rank3->name_rank = "Bậc 3";
    $rank3->basic_salary = "12000000";
    $rank3->save();
  }
}
