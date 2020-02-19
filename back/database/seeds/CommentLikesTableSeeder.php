<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class CommentLikesTableSeeder extends Seeder
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
            for($i = 0; $i < rand(0,30);$i++)
            {
                $randomNumber = rand(1,150);
                try{
                    $user->commentLikes()->attach($randomNumber,["id" => $user->id . $randomNumber]);
                }
                catch (Exception $er)
                {
                    continue;
                }
            }
        }
    }
}
