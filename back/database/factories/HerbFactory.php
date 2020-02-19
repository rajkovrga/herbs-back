
<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Herb;
use App\Models\Period;
use Faker\Generator as Faker;
use \App\Models\Pickpart;

function randomId($model,$id)
{
    $ids = $model::all()->pluck($id);
    return $ids[rand(0,count($ids)-1)];
}

$factory->define(Herb::class, function (Faker $faker) {
    return [
        'desc' => $faker->text(200),
        'srb_name' => $faker->text(100),
        'lat_name' => $faker->text(100),
        'toxic' => $faker->boolean(),
        'image_url' => 'image.jpg',
        'endangered' => $faker->boolean(),
        'period_id' => randomId(Period::class,'id'),
        'pickpart_id' => randomId(Pickpart::class,'id')
    ];
});

