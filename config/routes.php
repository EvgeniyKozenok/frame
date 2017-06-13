<?php

return [
    "root" => [
        "pattern" => "/",
        "method" => "",
        "action" => "John\\Frame\\TestController\\IndexController@index",
    ],
    "mob_phone" => [
        "pattern" => "/mobile",
        "action" => "John\\Frame\\TestController\\MobileController@show",
    ],






    "get_one_good" => [
        "pattern" => "/good/{id}",
        "method" => "GET",
        "variables" => [
            "id" => "\\d+"
        ],
        "action" => "John\\Frame\\TestController\\GoodController@getOneGood"
    ],
    "get_one_good_param" => [
        "pattern" => "/good/{id}/params/{name}",
        "method" => "GET",
        "variables" => [
            "id" => "\\d+"
        ],
        "action" => "John\\Frame\\TestController\\GoodController@getOneGoodWithParam",
        "middlewares" => [
            "test", "actionTime", "age:admin,moderator"
        ],
    ],
    "get_all_goods" => [
        "pattern" => "/good",
        "action" => "John\\Frame\\TestController\\GoodController@getAllGoods"
    ]
];