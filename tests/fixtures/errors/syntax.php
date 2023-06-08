<?php

return [
    [
        'json' => 'a[1, "", 3.14, [], {}]',
        'unexpected' => 'a',
        'position' => 0,
    ],
    [
        'json' => '[b1, "", 3.14, [], {}]',
        'unexpected' => 'b',
        'position' => 1,
    ],
    [
        'json' => '[1,c "", 3.14, [], {}]',
        'unexpected' => 'c',
        'position' => 3,
    ],
    [
        'json' => '[1, d"", 3.14, [], {}]',
        'unexpected' => 'd',
        'position' => 4,
    ],
    [
        'json' => '[1, "", e3.14, [], {}]',
        'unexpected' => 'e',
        'position' => 8,
    ],
    [
        'json' => '[1, "", 3.14, []f, {}]',
        'unexpected' => 'f',
        'position' => 17,
    ],
    [
        'json' => '[1, "", 3.14, [], g{}]',
        'unexpected' => 'g',
        'position' => 18,
    ],
    [
        'json' => '[1, "", 3.14, [], {h}]',
        'unexpected' => 'h',
        'position' => 19,
    ],
    [
        'json' => '[1, "", 3.14, [], {}i]',
        'unexpected' => 'i',
        'position' => 20,
    ],
    [
        'json' => '[1, "", 3.14, [], {}]j',
        'unexpected' => 'j',
        'position' => 21,
    ],
];
