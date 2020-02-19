<?php

use Illuminate\Database\Seeder;
use App\Models\Herb;

class HerbsTableSeeder extends Seeder
{
    /**
     * Run the database seeds
     *
     * @return void
     */
    public function run()
    {
      $herbs = factory(Herb::class,80)->create();
      return $herbs;
    }
}
