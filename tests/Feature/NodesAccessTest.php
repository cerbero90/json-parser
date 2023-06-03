<?php

use Cerbero\JsonParser\Exceptions\NodeNotFoundException;
use Cerbero\JsonParser\JsonParser;
use Cerbero\JsonParser\Tokens\Parser;

it('accesses nodes like object properties', function () {
    $json = new JsonParser(fixture('json/complex_object.json'));

    expect($json)
        ->id->toBe('0001')
        ->type->toBe('donut')
        ->name->toBe('Cake')
        ->ppu->toBe(0.55)
        ->batters->toBeArray()->toHaveKey('batter.3.type', "Devil's Food")
        ->topping->toBeArray()->toHaveKey('6.type', 'Maple');
});

it('accesses nodes like object properties when lazy loading', function () {
    $json = JsonParser::parse(fixture('json/complex_object.json'))->lazy();

    expect($json)
        ->id->toBe('0001')
        ->type->toBe('donut')
        ->name->toBe('Cake')
        ->ppu->toBe(0.55)
        ->batters->toBeInstanceof(Parser::class)->toHaveKey('batter.3.type', "Devil's Food")
        ->topping->toBeInstanceof(Parser::class)->toHaveKey('6.type', 'Maple');
});

it('accesses nodes like array keys', function () {
    $json = new JsonParser(fixture('json/complex_object.json'));

    expect($json['id'])->toBe('0001');
    expect($json['type'])->toBe('donut');
    expect($json['name'])->toBe('Cake');
    expect($json['ppu'])->toBe(0.55);
    expect($json['batters'])->toBeArray()->toHaveKey('batter.3.type', "Devil's Food");
    expect($json['topping'])->toBeArray()->toHaveKey('6.type', 'Maple');
});

it('accesses nodes like array keys when lazy loading', function () {
    $json = JsonParser::parse(fixture('json/complex_object.json'))->lazy();

    expect($json['id'])->toBe('0001');
    expect($json['type'])->toBe('donut');
    expect($json['name'])->toBe('Cake');
    expect($json['ppu'])->toBe(0.55);
    expect($json['batters'])->toBeInstanceof(Parser::class)->toHaveKey('batter.3.type', "Devil's Food");
    expect($json['topping'])->toBeInstanceof(Parser::class)->toHaveKey('6.type', 'Maple');
});

it('cannot access previous nodes', function () {
    $json = new JsonParser(fixture('json/complex_object.json'));

    expect($json->type)->toBe('donut');
    expect(fn () => $json->id)->toThrow(NodeNotFoundException::class, 'The node [id] was not found');
});

// it('accesses nodes by chaining properties and keys when lazy loading', function () {
//     $json = JsonParser::parse(fixture('json/complex_object.json'))->lazyPointer('');
//     dd($json->batters, $json->topping->toArray());
//     // dd($json->batters, $json->batters);

//     expect($json->batters->batter[2]->type)->toBe('Blueberry');
//     foreach ($json as $key => $value) {
//         dump("$key => " . (is_object($value) ? $value::class : $value));
//         is_object($value) && dump($value->toArray());
//     }
//     // expect($json->topping[3]->id)->toBe('5007');
// })->only();
