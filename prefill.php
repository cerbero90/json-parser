<?php
define('COL_DESCRIPTION', 0);
define('COL_HELP', 1);
define('COL_DEFAULT', 2);

$fields = [
    'package_name' => ['Package name', '<package> in https://github.com/vendor/package', basename(__DIR__)],
    'package_description' => ['Package very short description', '', ''],
];

$values = [];

$replacements = [
    ':package_name'        => function () use (&$values) {
        return $values['package_name'];
    },
    ':package_ns'          => function () use (&$values) {
        $value = ucwords(str_replace(['-', '_'], ' ', $values['package_name']));
        return str_replace(' ', '', $value);
    },
    ':package_title'       => function () use (&$values) {
        return ucwords(str_replace(['-', '_'], ' ', $values['package_name']));
    },
    ':package_description' => function () use (&$values) {
        return $values['package_description'];
    },
];

function read_from_console($prompt)
{
    if (function_exists('readline')) {
        $line = trim(readline($prompt));
        if (!empty($line)) {
            readline_add_history($line);
        }
    } else {
        echo $prompt;
        $line = trim(fgets(STDIN));
    }
    return $line;
}

function interpolate($text, $values)
{
    if (!preg_match_all('/\{(\w+)\}/', $text, $m)) {
        return $text;
    }
    foreach ($m[0] as $k => $str) {
        $f = $m[1][$k];
        $text = str_replace($str, $values[$f], $text);
    }
    return $text;
}

$modify = 'n';
do {
    if ($modify == 'q') {
        exit;
    }

    $values = [];

    echo "----------------------------------------------------------------------\n";
    echo "Please, provide the following information:\n";
    echo "----------------------------------------------------------------------\n";
    foreach ($fields as $f => $field) {
        $default = isset($field[COL_DEFAULT]) ? interpolate($field[COL_DEFAULT], $values) : '';
        $prompt = sprintf(
            '%s%s%s: ',
            $field[COL_DESCRIPTION],
            $field[COL_HELP] ? ' (' . $field[COL_HELP] . ')' : '',
            $field[COL_DEFAULT] !== '' ? ' [' . $default . ']' : ''
        );
        $values[$f] = read_from_console($prompt);
        if (empty($values[$f])) {
            $values[$f] = $default;
        }
    }
    echo "\n";

    echo "----------------------------------------------------------------------\n";
    echo "Please, check that everything is correct:\n";
    echo "----------------------------------------------------------------------\n";
    foreach ($fields as $f => $field) {
        echo $field[COL_DESCRIPTION] . ": $values[$f]\n";
    }
    echo "\n";
} while (($modify = strtolower(read_from_console('Modify files with these values? [y/N/q] '))) != 'y');
echo "\n";

$files = array_merge(
    glob(__DIR__ . '/*.md'),
    glob(__DIR__ . '/*.xml.dist'),
    glob(__DIR__ . '/composer.json'),
    glob(__DIR__ . '/src/Providers/*.php'),
    glob(__DIR__ . '/tests/*.php')
);
foreach ($files as $f) {
    $contents = file_get_contents($f);
    foreach ($replacements as $str => $func) {
        $contents = str_replace($str, $func(), $contents);
    }
    file_put_contents($f, $contents);
}

rename('./src/Providers/RenameMeServiceProvider.php', './src/Providers/' . $replacements[':package_ns']() . 'ServiceProvider.php');
rename('./tests/RenameMeTest.php', './tests/' . $replacements[':package_ns']() . 'Test.php');

echo "Done.\n";
echo "Now you should remove the file '" . basename(__FILE__) . "'.\n";
