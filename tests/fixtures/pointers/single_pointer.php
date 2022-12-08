<?php

return [
    'complex_array' => [
        '' => require __DIR__ . '/../parsing/complex_array.php',
        '/-' => [
            [
                "id" => "0001",
                "type" => "donut",
                "name" => "Cake",
                "ppu" => 0.55,
                "batters" => [
                    "batter" => [
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
                "topping" => [
                    [
                        "id" => "5001",
                        "type" => "None",
                    ],
                    [
                        "id" => "5002",
                        "type" => "Glazed",
                    ],
                    [
                        "id" => "5005",
                        "type" => "Sugar",
                    ],
                    [
                        "id" => "5007",
                        "type" => "Powdered Sugar",
                    ],
                    [
                        "id" => "5006",
                        "type" => "Chocolate with Sprinkles",
                    ],
                    [
                        "id" => "5003",
                        "type" => "Chocolate",
                    ],
                    [
                        "id" => "5004",
                        "type" => "Maple",
                    ],
                ],
            ],
            [
                "id" => "0002",
                "type" => "donut",
                "name" => "Raised",
                "ppu" => 0.55,
                "batters" => [
                    "batter" => [
                        [
                            "id" => "1001",
                            "type" => "Regular",
                        ],
                    ],
                ],
                "topping" => [
                    [
                        "id" => "5001",
                        "type" => "None",
                    ],
                    [
                        "id" => "5002",
                        "type" => "Glazed",
                    ],
                    [
                        "id" => "5005",
                        "type" => "Sugar",
                    ],
                    [
                        "id" => "5003",
                        "type" => "Chocolate",
                    ],
                    [
                        "id" => "5004",
                        "type" => "Maple",
                    ],
                ],
            ],
            [
                "id" => "0003",
                "type" => "donut",
                "name" => "Old Fashioned",
                "ppu" => 0.55,
                "batters" => [
                    "batter" => [
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
                "topping" => [
                    [
                        "id" => "5001",
                        "type" => "None",
                    ],
                    [
                        "id" => "5002",
                        "type" => "Glazed",
                    ],
                    [
                        "id" => "5003",
                        "type" => "Chocolate",
                    ],
                    [
                        "id" => "5004",
                        "type" => "Maple",
                    ],
                ],
            ],
        ],
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
        '' => require __DIR__ . '/../parsing/complex_object.php',
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
        '' => require __DIR__ . '/../parsing/simple_array.php',
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
