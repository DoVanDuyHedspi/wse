<?php

use Illuminate\Database\Seeder;
use App\Position;

class PositionsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $position1 = new Position();
    $position1->name = "Developer";
    $position1->save();

    $position2 = new Position();
    $position2->name = "Tester";
    $position2->save();

    $position3 = new Position();
    $position3->name = "Manager";
    $position3->save();

    $position4 = new Position();
    $position4->name = "Brse";
    $position4->save();

    $position5 = new Position();
    $position5->name = "CEO";
    $position5->save();
  }
}
