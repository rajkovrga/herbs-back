<?php

use Illuminate\Database\Seeder;
use \App\Models\Pickpart;

class PickPartTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ["list","plod","koren","telo"];
        foreach ($data as $d) {
            Pickpart::create(["name" => $d]);
        }
    }
}
