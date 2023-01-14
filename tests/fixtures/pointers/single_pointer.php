<?php

return [
    'complex_array' => [
        '' => $complexArray = require __DIR__ . '/../parsing/complex_array.php',
        '/-' => $complexArray,
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
            0 => [
                [
                    "id" => "1001",
                    "type" => "Regular",
                ],
                [
                    "id" => "1001",
                    "type" => "Regular",
                ],
                [
                    "id" => "1001",
                    "type" => "Regular",
                ],
            ],
            1 => [
                [
                    "id" => "1002",
                    "type" => "Chocolate",
                ],
                [
                    "id" => "1002",
                    "type" => "Chocolate",
                ],
            ],
            2 => [
                "id" => "1003",
                "type" => "Blueberry",
            ],
            3 => [
                "id" => "1004",
                "type" => "Devil's Food",
            ],
        ],
        '/-/batters/batter/-/id' => ['id' => ["1001", "1002", "1003", "1004", "1001", "1001", "1002"]],
    ],
    'complex_object' => [
        '' => require __DIR__ . '/../parsing/complex_object.php',
        '/-' => [],
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
        '' => [],
        '/-' => [],
        '/-1' => [],
        '/0' => [],
        '/foo' => [],
    ],
    'empty_object' => [
        '' => [],
        '/-' => [],
        '/-1' => [],
        '/0' => [],
        '/foo' => [],
    ],
    'simple_array' => [
        '' => $simpleArray = require __DIR__ . '/../parsing/simple_array.php',
        '/-' => $simpleArray,
        '/-1' => [],
        '/0' => [0 => 1],
        '/1' => [1 => ''],
        '/2' => [2 => 'foo'],
        '/3' => [3 => '"bar"'],
        '/4' => [4 => 'hej då'],
        '/5' => [5 => 3.14],
        '/6' => [6 => false],
        '/7' => [7 => null],
        '/8' => [8 => []],
        '/9' => [9 => []],
        '/10' => [],
        '/foo' => [],
    ],
    'simple_object' => [
        '' => require __DIR__ . '/../parsing/simple_object.php',
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
        '/' => ['' => 0],
        '/a~1b' => ['a/b' => 1],
        '/c%d' => ['c%d' => 2],
        '/e^f' => ['e^f' => 3],
        '/g|h' => ['g|h' => 4],
        '/i\\\\j' => ['i\\j' => 5],
        '/k\"l' => ['k"l' => 6],
        '/ ' => [' ' => 7],
        '/m~0n' => ['m~n' => 8],
    ],
];
