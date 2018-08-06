<?php

use Faker\Generator as Faker;

$factory->define(App\Thread::class, function (Faker $faker) {
    $title = $faker->sentence();

    return [
        'title' => $title,
        'body' => $faker->paragraph,
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'channel_id' => function () {
            return factory(App\Channel::class)->create()->id;
        },
        'visits' => 0,
        'slug' => str_slug($title),
        'locked' => false
    ];
});
