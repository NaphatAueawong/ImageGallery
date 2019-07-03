<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Image;
use App\User;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [

        'path' => Str::random(10),
        'type' => array_rand(['jpg', 'png']),
        'size' => rand(5, 10)
    ];
});
