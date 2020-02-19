<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Illuminate\Database\Seeder;
use App\Models\User;
class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::query()->cursor();
        $faker = Faker\Factory::create();
        foreach ($users as $user)
        {
            for($i = 0; $i < rand(0,10);$i++)
            {
                    $randomNumber = rand(1,80);
                    $user->comments()->attach($randomNumber,["desc" => $faker->text(300) ]);
            }
        }

    }
}
