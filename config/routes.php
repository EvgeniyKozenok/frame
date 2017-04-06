<?php

return [
    "root" => [
        "pattern" => "/",
        "method" => "",
        "action" => "John\\Frame\\TestController\\IndexController@index"
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
        "action" => "John\\Frame\\TestController\\GoodController@getOneGoodWithParam"
    ],
    "get_all_goods" => [
        "pattern" => "/good",
        "action" => "John\\Frame\\TestController\\GoodController@getAllGoods"
    ]
];