<?php

use Illuminate\Database\Seeder;
use \App\Models\User;
class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::query()->cursor();

        foreach ($users as $user)
        {
            for($i = 0; $i < rand(0,200);$i++)
            {
                $randomNumber = rand(1,80);
                try{
                    $user->likes()->attach($randomNumber,["id" => $user->id . $randomNumber]);
                }
                catch (Exception $er)
                {
                    continue;
                }

            }
        }
    }
}
