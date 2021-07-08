<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Post;
use App\Models\User;


$factory->define(Post::class, function (Faker $faker) {
    return [
        'image' => 'post-' . $faker->unique()->numberBetween(1,10) . '.png',
        'title' => $faker->word,
        'description' => $faker->text,
        'checked' => $faker->boolean,
        'user_id' => User::all()->random()->id,
    ];
});
