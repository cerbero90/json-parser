<?php

return [
    'complex_array' => [
        '/-/id' => ['id' => ['0001', '0002', '0003']],
        '/-/batters' => [
            'batters' => [
                [
                    'batter' => [
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
                [
                    'batter' => [
                        [
                            "id" => "1001",
                            "type" => "Regular",
                        ],
                    ],
                ],
                [
                    'batter' => [
                        [
                            "id" => "1001",
                            "type" => "Regular",
                        ],
                        [
                            "id" => "1002",
                            "type" => "Chocolate",
                        ],
                    ],
                ],
            ],
        ],
        '/-/batters/batter' => [
            'batter' => [
                [
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
                [
                    [
                        "id" => "1001",
                        "type" => "Regular",
                    ],
                ],
                [
                    [
                        "id" => "1001",
                        "type" => "Regular",
                    ],
                    [
                        "id" => "1002",
                        "type" => "Chocolate",
                    ],
                ],
            ],
        ],
        '/-/batters/batter/-' => [
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
            [
                "id" => "1001",
                "type" => "Regular",
            ],
            [
                "id" => "1001",
                "type" => "Regular",
            ],
            [
                "id" => "1002",
                "type" => "Chocolate",
            ],
        ],
        '/-/batters/batter/-/id' => ['id' => ["1001", "1002", "1003", "1004", "1001", "1001", "1002"]],
    ],
    'complex_object' => [
        '/id' => ['id' => '0001'],
        '/batters' => [
            'batters' => [
                'batter' => [
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
        ],
        '/batters/batter' => [
            'batter' => [
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
        '/batters/batter/-' => [
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
        '/batters/batter/-/id' => ['id' => ["1001", "1002", "1003", "1004"]],
    ],
    'empty_array' => [
        '/-' => [],
        '/-1' => [],
        '/0' => [],
        '/foo' => [],
    ],
    'empty_object' => [
        '/-' => [],
        '/-1' => [],
        '/0' => [],
        '/foo' => [],
    ],
    'simple_array' => [
        '/-' => [1, '', 'foo', '"bar"', 'hej då', 3.14, false, null, [], []],
        '/-1' => [],
        '/0' => [1],
        '/1' => [''],
        '/2' => ['foo'],
        '/3' => ['"bar"'],
        '/4' => ['hej då'],
        '/5' => [3.14],
        '/6' => [false],
        '/7' => [null],
        '/8' => [[]],
        '/9' => [[]],
        '/10' => [],
        '/foo' => [],
    ],
    'simple_object' => [
        '/-' => [],
        '/-1' => [],
        '/int' => ['int' => 1],
        '/empty_string' => ['empty_string' => ''],
        '/string' => ['string' => 'foo'],
        '/escaped_string' => ['escaped_string' => '"bar"'],
        '/\"escaped_key\"' => ['"escaped_key"' => 'baz'],
        '/unicode' => ['unicode' => "hej då"],
        '/float' => ['float' => 3.14],
        '/bool' => ['bool' => false],
        '/null' => ['null' => null],
        '/empty_array' => ['empty_array' => []],
        '/empty_object' => ['empty_object' => []],
        '/10' => [],
        '/foo' => [],
    ],
];
