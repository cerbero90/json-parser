<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Sources;
use Cerbero\JsonParser\Tokens\Parser;
use DirectoryIterator;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The dataset provider.
 *
 */
final class Dataset
{
    /**
     * Retrieve the dataset to test parsing
     *
     * @return Generator
     */
    public static function forParsing(): Generator
    {
        foreach (self::fixtures() as $fixture) {
            $name = $fixture->getBasename('.json');

            yield [
                file_get_contents($fixture->getRealPath()),
                require fixture("parsing/{$name}.php"),
            ];
        }
    }

    /**
     * Retrieve the fixtures
     *
     * @return Generator<int, DirectoryIterator>
     */
    protected static function fixtures(): Generator
    {
        foreach (new DirectoryIterator(fixture('json')) as $file) {
            if (!$file->isDot()) {
                yield $file;
            }
        }
    }

    /**
     * Retrieve the dataset to test invalid pointers
     *
     * @return Generator
     */
    public static function forInvalidPointers(): Generator
    {
        yield from ['abc', '/foo~2', '/~', ' '];
    }

    /**
     * Retrieve the dataset to test single pointers
     *
     * @return Generator
     */
    public static function forSinglePointers(): Generator
    {
        yield from self::forSinglePointersWithFixture('pointers/single_pointer.php');
    }

    /**
     * Retrieve the dataset to test single pointers with the given fixture
     *
     * @param string $path
     * @return Generator
     */
    private static function forSinglePointersWithFixture(string $path): Generator
    {
        $singlePointers = require fixture($path);

        foreach ($singlePointers as $fixture => $pointers) {
            $json = file_get_contents(fixture("json/{$fixture}.json"));

            foreach ($pointers as $pointer => $value) {
                yield [$json, $pointer, $value];
            }
        }
    }

    /**
     * Retrieve the dataset to test single pointers eager loading
     *
     * @return Generator
     */
    public static function forSinglePointersToArray(): Generator
    {
        yield from self::forSinglePointersWithFixture('pointers/single_pointer_to_array.php');
    }

    /**
     * Retrieve the dataset to test the key update
     *
     * @return Generator
     */
    public static function forKeyUpdate(): Generator
    {
        $json = fixture('json/complex_object.json');
        $pointers = [
            '/type' => function ($value, &$key) {
                $key = 'foo';
            },
        ];

        yield [$json, $pointers, ['foo' => 'donut']];
    }

    /**
     * Retrieve the dataset to test multiple pointers
     *
     * @return Generator
     */
    public static function forMultiplePointers(): Generator
    {
        yield from self::forMultiplePointersWithFixture('pointers/multiple_pointers.php');
    }

    /**
     * Retrieve the dataset to test multiple pointers with the given fixture
     *
     * @param string $path
     * @return Generator
     */
    private static function forMultiplePointersWithFixture(string $path): Generator
    {
        $multiplePointers = require fixture($path);

        foreach ($multiplePointers as $fixture => $valueByPointers) {
            $json = file_get_contents(fixture("json/{$fixture}.json"));

            foreach ($valueByPointers as $pointers => $value) {
                yield [$json, explode(',', $pointers), $value];
            }
        }
    }

    /**
     * Retrieve the dataset to test multiple pointers eager loading
     *
     * @return Generator
     */
    public static function forMultiplePointersToArray(): Generator
    {
        yield from self::forMultiplePointersWithFixture('pointers/multiple_pointers_to_array.php');
    }

    /**
     * Retrieve the dataset to test intersecting pointers with wildcards
     *
     * @return Generator
     */
    public static function forIntersectingPointersWithWildcards(): Generator
    {
        $json = fixture('json/complex_object.json');

        $pointers = [
            '/topping/6/type' => fn (string $value) => "$value @ /topping/6/type",
            '/topping/-/type' => fn (string $value) => "$value @ /topping/-/type",
            '/topping/0/type' => fn (string $value) => "$value @ /topping/0/type",
            '/topping/2/type' => fn (string $value) => "$value @ /topping/2/type",
        ];

        $parsed = [
            'type' => [
                'None @ /topping/0/type',
                'Glazed @ /topping/-/type',
                'Sugar @ /topping/2/type',
                'Powdered Sugar @ /topping/-/type',
                'Chocolate with Sprinkles @ /topping/-/type',
                'Chocolate @ /topping/-/type',
                'Maple @ /topping/6/type',
            ]
        ];

        yield [$json, $pointers, $parsed];
    }

    /**
     * Retrieve the dataset to test intersecting pointers
     *
     * @return Generator
     */
    public static function forIntersectingPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $message = 'The pointers [%s] and [%s] are intersecting';
        $pointersByIntersection = [
            '/topping,/topping/0' => [
                '/topping',
                '/topping/0',
            ],
            '/topping/0,/topping' => [
                '/topping/0',
                '/topping',
            ],
            '/topping,/topping/-' => [
                '/topping',
                '/topping/-',
            ],
            '/topping/-,/topping' => [
                '/topping/-',
                '/topping',
            ],
            '/topping/0/type,/topping' => [
                '/topping/0/type',
                '/topping/-/type',
                '/topping',
            ],
            '/topping,/topping/-/type' => [
                '/topping',
                '/topping/-/type',
                '/topping/0/type',
            ],
            '/topping/-/type,/topping/-/type/baz' => [
                '/topping/-/type',
                '/topping/-/types',
                '/topping/-/type/baz',
            ],
            '/topping/-/type/baz,/topping/-/type' => [
                '/topping/-/type/baz',
                '/topping/-/type',
                '/topping/-/types',
            ],
        ];

        foreach ($pointersByIntersection as $intersection => $pointers) {
            yield [$json, $pointers, vsprintf($message, explode(',', $intersection))];
        }
    }

    /**
     * Retrieve the dataset to test single lazy pointers
     *
     * @return Generator
     */
    public static function forSingleLazyPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $sequenceByPointer = [
            '' => [
                fn ($value, $key) => $key->toBe('id')->and($value->value)->toBe('0001'),
                fn ($value, $key) => $key->toBe('type')->and($value->value)->toBe('donut'),
                fn ($value, $key) => $key->toBe('name')->and($value->value)->toBe('Cake'),
                fn ($value, $key) => $key->toBe('ppu')->and($value->value)->toBe(0.55),
                fn ($value, $key) => $key->toBe('batters')->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe('topping')->and($value->value)->toBeInstanceOf(Parser::class),
            ],
            '/batters/batter/-' => [
                fn ($value, $key) => $key->toBe(0)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(1)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(2)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(3)->and($value->value)->toBeInstanceOf(Parser::class),
            ],
            '/topping/-' => [
                fn ($value, $key) => $key->toBe(0)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(1)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(2)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(3)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(4)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(5)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(6)->and($value->value)->toBeInstanceOf(Parser::class),
            ],
        ];

        foreach ($sequenceByPointer as $pointer => $sequence) {
            yield [$json, $pointer, $sequence];
        }
    }

    /**
     * Retrieve the dataset to test multiple lazy pointers
     *
     * @return Generator
     */
    public static function forMultipleLazyPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $sequenceByPointer = [
            '/topping,/batters' => [
                fn ($value, $key) => $key->toBe('batters')->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe('topping')->and($value->value)->toBeInstanceOf(Parser::class),
            ],
            '/topping/-,/batters/batter' => [
                fn ($value, $key) => $key->toBe('batter')->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(0)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(1)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(2)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(3)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(4)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(5)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(6)->and($value->value)->toBeInstanceOf(Parser::class),
            ],
        ];

        foreach ($sequenceByPointer as $pointers => $sequence) {
            yield [$json, explode(',', $pointers), $sequence];
        }
    }

    /**
     * Retrieve the dataset to test recursive lazy loading
     *
     * @return Generator
     */
    public static function forRecursiveLazyLoading(): Generator
    {
        $json = fixture('json/complex_object.json');
        $expectedByKeys = [
            'batters,batter' => [
                ['id' => '1001', 'type' => 'Regular'],
                ['id' => '1002', 'type' => 'Chocolate'],
                ['id' => '1003', 'type' => 'Blueberry'],
                ['id' => '1004', 'type' => 'Devil\'s Food'],
            ],
            'topping' => [
                ['id' => '5001', 'type' => 'None'],
                ['id' => '5002', 'type' => 'Glazed'],
                ['id' => '5005', 'type' => 'Sugar'],
                ['id' => '5007', 'type' => 'Powdered Sugar'],
                ['id' => '5006', 'type' => 'Chocolate with Sprinkles'],
                ['id' => '5003', 'type' => 'Chocolate'],
                ['id' => '5004', 'type' => 'Maple'],
            ],
        ];

        foreach ($expectedByKeys as $keys => $expected) {
            $keys = explode(',', $keys);
            yield [$json, '/' . $keys[0], $keys, $expected];
        }
    }

    /**
     * Retrieve the dataset to test mixed pointers
     *
     * @return Generator
     */
    public static function forMixedPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $pointersList = [
            [
                '/name' => fn (string $name) => "name_{$name}",
            ],
            [
                '/id' => fn (string $id) => "id_{$id}",
                '/type' => fn (string $type) => "type_{$type}",
            ],
        ];
        $lazyPointers = [
            [
                '/batters/batter' => fn (Parser $batter) => $batter::class,
            ],
            [
                '/batters' => fn (Parser $batters) => $batters::class,
                '/topping' => fn (Parser $topping) => $topping::class,
            ],
        ];
        $expected = [
            [
                'name' => 'name_Cake',
                'batter' => Parser::class,
            ],
            [
                'id' => 'id_0001',
                'type' => 'type_donut',
                'batters' => Parser::class,
                'topping' => Parser::class,
            ],
        ];

        foreach ($pointersList as $index => $pointers) {
            yield [$json, $pointers, $lazyPointers[$index], $expected[$index]];
        }
    }

    /**
     * Retrieve the dataset to test syntax errors
     *
     * @return Generator
     */
    public static function forSyntaxErrors(): Generator
    {
        yield from require fixture('errors/syntax.php');
    }

    /**
     * Retrieve the dataset to test decoding errors patching
     *
     * @return Generator
     */
    public static function forDecodingErrorsPatching(): Generator
    {
        $patches = [null, 'baz', 123];
        $json = '[1a, ""b, "foo", 3.1c4, falsed, null, [1, 2e], {"bar": 1, "baz"f: 2}]';
        $patchJson = fn (mixed $patch) => [$patch, $patch, 'foo', $patch, $patch, null, $patch, $patch];

        foreach ($patches as $patch) {
            yield [$json, $patch, $patchJson($patch)];
        }

        $patch = fn (DecodedValue $decoded) => strrev($decoded->json);
        $patched = ['a1', 'b""', 'foo', '4c1.3', 'deslaf', null, ']e2,1[', '}2:f"zab",1:"rab"{'];

        yield [$json, fn () => $patch, $patched];
    }

    /**
     * Retrieve the dataset to test sources requiring Guzzle
     *
     * @return Generator
     */
    public static function forSourcesRequiringGuzzle(): Generator
    {
        $sources = [Sources\Endpoint::class, Sources\Psr7Request::class];

        foreach ($sources as $source) {
            yield Mockery::mock($source)
                ->makePartial()
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('guzzleIsInstalled')
                ->andReturn(false)
                ->getMock();
        }
    }

    /**
     * Retrieve the dataset to test decoders
     *
     * @return Generator
     */
    public static function forDecoders(): Generator
    {
        $json = '{"foo":"bar"}';
        $values = [
            true => ['foo' => 'bar'],
            false => (object) ['foo' => 'bar'],
        ];

        foreach ([true, false] as $decodesToArray) {
            yield [$decodesToArray, $json, $values[$decodesToArray]];
        }
    }

    /**
     * Retrieve the dataset to test sources
     *
     * @return Generator
     */
    public static function forSources(): Generator
    {
        $path = fixture('json/simple_array.json');
        $json = file_get_contents($path);
        $size = strlen($json);
        $request = new Psr7Request('GET', 'foo');

        $stream = Mockery::mock(StreamInterface::class)
            ->shouldReceive([
                'getSize' => $size,
                'isReadable' => true,
            ])
            ->getMock();

        $response = Mockery::mock(ResponseInterface::class)
            ->shouldReceive('getBody')
            ->andReturn($stream)
            ->getMock();

        $client = Mockery::mock(Client::class)
            ->shouldReceive('get', 'sendRequest')
            ->andReturn($response)
            ->getMock();

        $endpoint = Mockery::mock(Sources\Endpoint::class, ['https://example.com'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('guzzle')
            ->andReturn($client)
            ->getMock();

        $laravelClientRequest = Mockery::mock(Sources\LaravelClientRequest::class, [new Request($request)])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('guzzle')
            ->andReturn($client)
            ->getMock();

        $psr7Response = Mockery::mock(Psr7Response::class)
            ->shouldReceive('getBody')
            ->andReturn($stream)
            ->getMock();

        $psr7Request = Mockery::mock(Sources\Psr7Request::class, [$request])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('guzzle')
            ->andReturn($client)
            ->getMock();

        $sources = [
            new Sources\AnySource(new Sources\Json($json)),
            new Sources\CustomSource(new Sources\Json($json)),
            $endpoint,
            new Sources\Filename($path),
            new Sources\IterableSource(str_split($json)),
            new Sources\Json($json),
            new Sources\JsonResource(fopen($path, 'rb')),
            $laravelClientRequest,
            new Sources\LaravelClientResponse(new Response($psr7Response)),
            new Sources\Psr7Message($response),
            $psr7Request,
            new Sources\Psr7Stream($stream),
        ];

        foreach ($sources as $source) {
            yield [$source, $size];
        }
    }
}
