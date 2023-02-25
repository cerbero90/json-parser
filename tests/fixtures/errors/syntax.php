<?php

return [
    [
        'json' => 'a[1, "", 3.14, [], {}]',
        'unexpected' => 'a',
        'position' => 1,
    ],
    [
        'json' => '[b1, "", 3.14, [], {}]',
        'unexpected' => 'b',
        'position' => 2,
    ],
    [
        'json' => '[1,c "", 3.14, [], {}]',
        'unexpected' => 'c',
        'position' => 4,
    ],
    [
        'json' => '[1, d"", 3.14, [], {}]',
        'unexpected' => 'd',
        'position' => 5,
    ],
    [
        'json' => '[1, "", e3.14, [], {}]',
        'unexpected' => 'e',
        'position' => 9,
    ],
    [
        'json' => '[1, "", 3.14, []f, {}]',
        'unexpected' => 'f',
        'position' => 18,
    ],
    [
        'json' => '[1, "", 3.14, [], g{}]',
        'unexpected' => 'g',
        'position' => 19,
    ],
    [
        'json' => '[1, "", 3.14, [], {h}]',
        'unexpected' => 'h',
        'position' => 20,
    ],
    [
        'json' => '[1, "", 3.14, [], {}i]',
        'unexpected' => 'i',
        'position' => 21,
    ],
    [
        'json' => '[1, "", 3.14, [], {}]j',
        'unexpected' => 'j',
        'position' => 22,
    ],
];
