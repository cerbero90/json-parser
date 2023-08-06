<?php

return [
    'complex_array' => [
        '/-1,/-2' => [],
        '/-/id,/-/batters/batter/-/type' => [
            [
                'id' => '0001',
                'type' => 'Regular',
            ],
            [
                'type' => 'Chocolate',
            ],
            [
                'type' => 'Blueberry',
            ],
            [
                'type' => 'Devil\'s Food',
                'id' => '0002',
            ],
            [
                'type' => 'Regular',
                'id' => '0003',
            ],
            [
                'type' => 'Regular',
            ],
            [
                'type' => 'Chocolate',
            ],
        ],
        '/-/name,/-/topping/-/type,/-/id' => [
            [
                'id' => '0001',
                'name' => 'Cake',
                'type' => 'None',
            ],
            [
                'type' => 'Glazed',
            ],
            [
                'type' => 'Sugar',
            ],
            [
                'type' => 'Powdered Sugar',
            ],
            [
                'type' => 'Chocolate with Sprinkles',
            ],
            [
                'type' => 'Chocolate',
            ],
            [
                'type' => 'Maple',
                'id' => '0002',
                'name' => 'Raised',
            ],
            [
                'type' => 'None',
            ],
            [
                'type' => 'Glazed',
            ],
            [
                'type' => 'Sugar',
            ],
            [
                'type' => 'Chocolate',
            ],
            [
                'type' => 'Maple',
                'id' => '0003',
                'name' => 'Old Fashioned',
            ],
            [
                'type' => 'None',
            ],
            [
                'type' => 'Glazed',
            ],
            [
                'type' => 'Chocolate',
            ],
            [
                'type' => 'Maple',
            ],
        ],
        '/-/batters/batter/-,/-/name' => [
            [
                'name' => 'Cake',
                [
                    'id' => '1001',
                    'type' => 'Regular',
                ],
                [
                    'id' => '1002',
                    'type' => 'Chocolate',
                ],
                [
                    'id' => '1003',
                    'type' => 'Blueberry',
                ],
                [
                    'id' => '1004',
                    'type' => 'Devil\'s Food',
                ],
            ],
            [
                'name' => 'Raised',
                [
                    'id' => '1001',
                    'type' => 'Regular',
                ],
            ],
            [
                'name' => 'Old Fashioned',
                [
                    'id' => '1001',
                    'type' => 'Regular',
                ],
                [
                    'id' => '1002',
                    'type' => 'Chocolate',
                ],
            ],
        ],
    ],
    'complex_object' => [
        '/-1,/-2' => [],
        '/id,/batters/batter/-/type' => [
            [
                'id' => '0001',
                'type' => 'Regular',
            ],
            [
                'type' => 'Chocolate',
            ],
            [
                'type' => 'Blueberry',
            ],
            [
                'type' => 'Devil\'s Food',
            ],
        ],
        '/name,/topping/-/type,/id' => [
            [
                'id' => '0001',
                'name' => 'Cake',
                'type' => 'None',
            ],
            [
                'type' => 'Glazed',
            ],
            [
                'type' => 'Sugar',
            ],
            [
                'type' => 'Powdered Sugar',
            ],
            [
                'type' => 'Chocolate with Sprinkles',
            ],
            [
                'type' => 'Chocolate',
            ],
            [
                'type' => 'Maple',
            ],
        ],
        '/batters/batter/-,/type' => [
            'type' => 'donut',
            [
                "id" => "1001",
                "type" => "Regular",
            ],
            [
                "id" => "1002",
                "type" => "Chocolate",
            ],
            [
                "id" => "1003",
                "type" => "Blueberry",
            ],
            [
                "id" => "1004",
                "type" => "Devil's Food",
            ],
        ],
    ],
    'empty_array' => [
        '/-1,/-2' => [],
        '/foo,/bar' => [],
    ],
    'empty_object' => [
        '/-1,/-2' => [],
        '/foo,/bar' => [],
    ],
    'simple_array' => [
        '/-1,/-2' => [],
        '/0,/1' => [0 => 1, 1 => ''],
        '/1,/0' => [0 => 1, 1 => ''],
        '/0,/2' => [0 => 1, 2 => 'foo'],
        '/2,/3' => [2 => 'foo', 3 => '"bar"'],
        '/3,/4,/5' => [3 => '"bar"', 4 => 'hej då', 5 => 3.14],
        '/4,/5,/3' => [3 => '"bar"', 4 => 'hej då', 5 => 3.14],
        '/6,/7,/8,/9' => [6 => false, 7 => null, 8 => [], 9 => []],
        '/9,/8,/7,/6' => [6 => false, 7 => null, 8 => [], 9 => []],
    ],
    'simple_object' => [
        '/-1,/-2' => [],
        '/int,/empty_string' => ['int' => 1, 'empty_string' => ''],
        '/empty_string,/int' => ['int' => 1, 'empty_string' => ''],
        '/string,/escaped_string,/\"escaped_key\"' => ['string' => 'foo', 'escaped_string' => '"bar"', '"escaped_key"' => 'baz'],
        '/unicode,/bool,/empty_array' => ['unicode' => "hej då", 'bool' => false, 'empty_array' => []],
        '/,/a~1b,/c%d,/e^f,/g|h,/i\\\\j' => ['' => 0, 'a/b' => 1, 'c%d' => 2, 'e^f' => 3, 'g|h' => 4, 'i\\j' => 5],
        '/k\"l,/ ,/m~0n' => ['k"l' => 6, ' ' => 7, 'm~n' => 8],
    ],
];
