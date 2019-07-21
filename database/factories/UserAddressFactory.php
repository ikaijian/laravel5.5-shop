<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserAddress::class, function (Faker $faker) {
    $userIds = \App\Models\User::all()->pluck('id')->toArray();
    return [
        //
        ["北京市", "市辖区", "东城区"],
        ["河北省", "石家庄市", "长安区"],
        ["江苏省", "南京市", "浦口区"],
        ["江苏省", "苏州市", "相城区"],
        ["广东省", "深圳市", "福田区"],
        ["广东省", "广州市", "天河区区"],
    ];

    $address = $faker->randomElement($addresses);

    return [
        'user_id' => $faker->randomElement($userIds),
        'province' => $address[0],
        'city' => $address[1],
        'district' => $address[2],
        'address' => sprintf('第%d街道第%d号', $faker->randomNumber(2), $faker->randomNumber(3)),
        'zip' => $faker->postcode,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
    ];
});
