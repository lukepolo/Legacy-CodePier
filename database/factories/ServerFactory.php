<?php

$factory->define(\App\Models\Server\Server::class, function (Faker\Generator $faker) {
    return [
        'sudo_password' => $faker->password,
        'public_ssh_key' => $faker->randomAscii,
        'private_ssh_key' => $faker->randomAscii,
        'ip' => $faker->ipv4,
        'port' => $faker->randomNumber(5)
    ];

});