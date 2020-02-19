<?php

use Illuminate\Database\Seeder;
use \App\Models\Period;

class PeriodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ["januar","februar","mart","april","maj","jun","jul","avgust","septembar","oktobar","novembar","decembar"];

        foreach ($data as $d) {
            Period::query()->create(["name" => $d]);
        }
    }
}
