# 🧩 JSON Parser

[![Author][ico-author]][link-author]
[![PHP Version][ico-php]][link-php]
[![Build Status][ico-actions]][link-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![PHPStan Level][ico-phpstan]][link-phpstan]
[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![PSR-7][ico-psr7]][link-psr7]
[![PSR-12][ico-psr12]][link-psr12]
[![Total Downloads][ico-downloads]][link-downloads]

Zero-dependencies pull parser to read large JSON from any source in a memory-efficient way.


## 📦 Install

Via Composer:

``` bash
composer require cerbero/json-parser
```

## 🔮 Usage

* [👣 Basics](#-basics)
* [💧 Sources](#-sources)
* [🎯 Pointers](#-pointers)
* [🐼 Lazy pointers](#-lazy-pointers)
* [⚙️ Decoders](#%EF%B8%8F-decoders)
* [💢 Errors handling](#-errors-handling)
* [⏳ Progress](#-progress)
* [🛠 Settings](#-settings)


### 👣 Basics

JSON Parser provides a minimal API to read large JSON from any source:

```php
// a source is anything that can provide a JSON, in this case an endpoint
$source = 'https://randomuser.me/api/1.4?seed=json-parser&results=5';

foreach (new JsonParser($source) as $key => $value) {
    // instead of loading the whole JSON, we keep in memory only one key and value at a time
}
```

Depending on our code style, we can instantiate the parser in 3 different ways:

```php
use Cerbero\JsonParser\JsonParser;
use function Cerbero\JsonParser\parseJson;


// classic object instantiation
new JsonParser($source);

// static instantiation
JsonParser::parse($source);

// namespaced function
parseJson($source);
```

If we don't want to use `foreach()` to loop through each key and value, we can chain the `traverse()` method:

```php
JsonParser::parse($source)->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
    // lazily load one key and value at a time, we can also access the parser if needed
});

// no foreach needed
```

> ⚠️ Please note the parameters order of the callback: the value is passed before the key.

### 💧 Sources

A JSON source is any data point that provides a JSON. A wide range of sources are supported by default:
- **strings**, e.g. `{"foo":"bar"}`
- **iterables**, i.e. arrays or instances of `Traversable`
- **file paths**, e.g. `/path/to/large.json`
- **resources**, e.g. streams
- **API endpoint URLs**, e.g. `https://endpoint.json` or any instance of `Psr\Http\Message\UriInterface`
- **PSR-7 requests**, i.e. any instance of `Psr\Http\Message\RequestInterface`
- **PSR-7 messages**, i.e. any instance of `Psr\Http\Message\MessageInterface`
- **PSR-7 streams**, i.e. any instance of `Psr\Http\Message\StreamInterface`
- **Laravel HTTP client requests**, i.e. any instance of `Illuminate\Http\Client\Request`
- **Laravel HTTP client responses**, i.e. any instance of `Illuminate\Http\Client\Response`
- **user-defined sources**, i.e. any instance of `Cerbero\JsonParser\Sources\Source`

If the source we need to parse is not supported by default, we can implement our own custom source.

<details><summary><b>Click here to see how to implement a custom source.</b></summary>

To implement a custom source, we need to extend `Source` and implement 3 methods:

```php
use Cerbero\JsonParser\Sources\Source;
use Traversable;

class CustomSource extends Source
{
    public function getIterator(): Traversable
    {
        // return a Traversable holding the JSON source, e.g. a Generator yielding chunks of JSON
    }

    public function matches(): bool
    {
        // return TRUE if this class can handle the JSON source
    }

    protected function calculateSize(): ?int
    {
        // return the size of the JSON in bytes or NULL if it can't be calculated
    }
}
```

The parent class `Source` gives us access to 2 properties:
- `$source`: the JSON source we pass to the parser, i.e.: `new JsonParser($source)`
- `$config`: the configuration we set by chaining methods like `$parser->pointer('/foo')`

The method `getIterator()` defines the logic to read the JSON source in a memory-efficient way. It feeds the parser with small pieces of JSON. Please refer to the [already existing sources](https://github.com/cerbero90/json-parser/tree/master/src/Sources) to see some implementations.

The method `matches()` determines whether the JSON source passed to the parser can be handled by our custom implementation. In other words, we are telling the parser if it should use our class for the JSON to parse.

Finally, `calculateSize()` computes the whole size of the JSON source. It's used to track the [parsing progress](#-progress), however it's not always possible to know the size of a JSON source. In this case, or if we don't need to track the progress, we can return `null`.

Now that we have implemented our custom source, we can pass it to the parser:

```php
$json = JsonParser::parse(new CustomSource($source));

foreach ($json as $key => $value) {
    // process one key and value of $source at a time
}
```

If you find yourself implementing the same custom source in different projects, feel free to send a PR and we will consider to support your custom source by default. Thank you in advance for any contribution!
</details>


### 🎯 Pointers

A JSON pointer is a [standard](https://www.rfc-editor.org/rfc/rfc6901) used to point to nodes within a JSON. This package leverages JSON pointers to extract only some sub-trees from large JSONs.

Consider [this JSON](https://randomuser.me/api/1.4?seed=json-parser&results=5) for example. To extract only the first gender and avoid parsing the rest of the JSON, we can set the `/results/0/gender` pointer:

```php
$json = JsonParser::parse($source)->pointer('/results/0/gender');

foreach ($json as $key => $value) {
    // 1st and only iteration: $key === 'gender', $value === 'female'
}
```

JSON Parser takes advantage of the `-` wildcard to point to any array index, so we can extract all the genders with the `/results/-/gender` pointer:

```php
$json = JsonParser::parse($source)->pointer('/results/-/gender');

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'gender', $value === 'female'
    // 2nd iteration: $key === 'gender', $value === 'female'
    // 3rd iteration: $key === 'gender', $value === 'male'
    // and so on for all the objects in the array...
}
```

If we want to extract more sub-trees, we can set multiple pointers. Let's extract all genders and countries:

```php
$json = JsonParser::parse($source)->pointers(['/results/-/gender', '/results/-/location/country']);

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'gender', $value === 'female'
    // 2nd iteration: $key === 'country', $value === 'Germany'
    // 3rd iteration: $key === 'gender', $value === 'female'
    // 4th iteration: $key === 'country', $value === 'Mexico'
    // and so on for all the objects in the array...
}
```

> ⚠️ Intersecting pointers like `/foo` and `/foo/bar` is not allowed but intersecting wildcards like `foo/-/bar` and `foo/0/bar` is possible.

We can also specify a callback to execute when JSON pointers are found. This is handy when we have different pointers and we need to run custom logic for each of them:

```php
$json = JsonParser::parse($source)->pointers([
    '/results/-/gender' => fn (string $gender, string $key) => new Gender($gender),
    '/results/-/location/country' => fn (string $country, string $key) => new Country($country),
]);

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'gender', $value instanceof Gender
    // 2nd iteration: $key === 'country', $value instanceof Country
    // and so on for all the objects in the array...
}
```

> ⚠️ Please note the parameters order of the callbacks: the value is passed before the key.

The same can also be achieved by chaining the method `pointer()` multiple times:

```php
$json = JsonParser::parse($source)
    ->pointer('/results/-/gender', fn (string $gender, string $key) => new Gender($gender))
    ->pointer('/results/-/location/country', fn (string $country, string $key) => new Country($country));

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'gender', $value instanceof Gender
    // 2nd iteration: $key === 'country', $value instanceof Country
    // and so on for all the objects in the array...
}
```

Pointer callbacks can also be used to customize a key. We can achieve that by updating the key **reference**:

```php
$json = JsonParser::parse($source)->pointer('/results/-/name/first', function (string $name, string &$key) {
    $key = 'first_name';
});

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'first_name', $value === 'Sara'
    // 2nd iteration: $key === 'first_name', $value === 'Andrea'
    // and so on for all the objects in the array...
}
```

If the callbacks are enough to handle the pointers and we don't need to run any common logic for all pointers, we can avoid to manually call `foreach()` by chaining the method `traverse()`:

```php
JsonParser::parse($source)
    ->pointer('/-/gender', $this->handleGender(...))
    ->pointer('/-/location/country', $this->handleCountry(...))
    ->traverse();

// no foreach needed
```

Otherwise if some common logic for all pointers is needed but we prefer methods chaining to manual loops, we can pass a callback to the `traverse()` method:

```php
JsonParser::parse($source)
    ->pointer('/results/-/gender', fn (string $gender, string $key) => new Gender($gender))
    ->pointer('/results/-/location/country', fn (string $country, string $key) => new Country($country))
    ->traverse(function (Gender|Country $value, string $key, JsonParser $parser) {
        // 1st iteration: $key === 'gender', $value instanceof Gender
        // 2nd iteration: $key === 'country', $value instanceof Country
        // and so on for all the objects in the array...
    });

// no foreach needed
```

> ⚠️ Please note the parameters order of the callbacks: the value is passed before the key.

Sometimes the sub-trees extracted by pointers are small enough to be kept entirely in memory. In such cases, we can chain `toArray()` to eager load the extracted sub-trees into an array:

```php
// ['gender' => 'female', 'country' => 'Germany']
$array = JsonParser::parse($source)->pointers(['/results/0/gender', '/results/0/location/country'])->toArray();
```

### 🐼 Lazy pointers

JSON Parser only keeps one key and one value in memory at a time. However, if the value is a large array or object, it may be inefficient or even impossible to keep it all in memory.

To solve this problem, we can use lazy pointers. These pointers recursively keep in memory only one key and one value at a time for any nested array or object.

```php
$json = JsonParser::parse($source)->lazyPointer('/results/0/name');

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'name', $value instanceof Parser
}
```

Lazy pointers return a lightweight instance of `Cerbero\JsonParser\Tokens\Parser` instead of the actual large value. To lazy load nested keys and values, we can then loop through the parser:

```php
$json = JsonParser::parse($source)->lazyPointer('/results/0/name');

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'name', $value instanceof Parser
    foreach ($value as $nestedKey => $nestedValue) {
        // 1st iteration: $nestedKey === 'title', $nestedValue === 'Mrs'
        // 2nd iteration: $nestedKey === 'first', $nestedValue === 'Sara'
        // 3rd iteration: $nestedKey === 'last', $nestedValue === 'Meder'
    }
}
```

As mentioned above, lazy pointers are recursive. This means that no nested objects or arrays will ever be kept in memory:

```php
$json = JsonParser::parse($source)->lazyPointer('/results/0/location');

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'location', $value instanceof Parser
    foreach ($value as $nestedKey => $nestedValue) {
        // 1st iteration: $nestedKey === 'street', $nestedValue instanceof Parser
        // 2nd iteration: $nestedKey === 'city', $nestedValue === 'Sontra'
        // ...
        // 6th iteration: $nestedKey === 'coordinates', $nestedValue instanceof Parser
        // 7th iteration: $nestedKey === 'timezone', $nestedValue instanceof Parser
    }
}
```

To lazily parse the entire JSON, we can simply chain the `lazy()` method:

```php
foreach (JsonParser::parse($source)->lazy() as $key => $value) {
    // 1st iteration: $key === 'results', $value instanceof Parser
    // 2nd iteration: $key === 'info', $value instanceof Parser
}
```

We can recursively wrap any instance of `Cerbero\JsonParser\Tokens\Parser` by chaining `wrap()`. This lets us wrap lazy loaded JSON arrays and objects into classes with advanced functionalities, like mapping or filtering:

```php
$json = JsonParser::parse($source)
    ->wrap(fn (Parser $parser) => new MyWrapper(fn () => yield from $parser))
    ->lazy();

foreach ($json as $key => $value) {
    // 1st iteration: $key === 'results', $value instanceof MyWrapper
    foreach ($value as $nestedKey => $nestedValue) {
        // 1st iteration: $nestedKey === 0, $nestedValue instanceof MyWrapper
        // 2nd iteration: $nestedKey === 1, $nestedValue instanceof MyWrapper
        // ...
    }
}
```

> ℹ️ If your wrapper class implements the method `toArray()`, such method will be called when eager loading sub-trees into an array.

Lazy pointers also have all the other functionalities of normal pointers: they accept callbacks, can be set one by one or all together, can be eager loaded into an array and can be mixed with normal pointers as well:

```php
// set custom callback to run only when names are found
$json = JsonParser::parse($source)->lazyPointer('/results/-/name', fn (Parser $name) => $this->handleName($name));

// set multiple lazy pointers one by one
$json = JsonParser::parse($source)
    ->lazyPointer('/results/-/name', fn (Parser $name) => $this->handleName($name))
    ->lazyPointer('/results/-/location', fn (Parser $location) => $this->handleLocation($location));

// set multiple lazy pointers all together
$json = JsonParser::parse($source)->lazyPointers([
    '/results/-/name' => fn (Parser $name) => $this->handleName($name)),
    '/results/-/location' => fn (Parser $location) => $this->handleLocation($location)),
]);

// eager load lazy pointers into an array
// ['name' => ['title' => 'Mrs', 'first' => 'Sara', 'last' => 'Meder'], 'street' => ['number' => 46, 'name' => 'Römerstraße']]
$array = JsonParser::parse($source)->lazyPointers(['/results/0/name', '/results/0/location/street'])->toArray();

// mix pointers and lazy pointers
$json = JsonParser::parse($source)
    ->pointer('/results/-/gender', fn (string $gender) => $this->handleGender($gender))
    ->lazyPointer('/results/-/name', fn (Parser $name) => $this->handleName($name));
```

### ⚙️ Decoders

By default JSON Parser uses the built-in PHP function `json_decode()` to decode one key and value at a time.

Normally it decodes values to associative arrays but, if we prefer to decode values to objects, we can set a custom decoder:

```php
use Cerbero\JsonParser\Decoders\JsonDecoder;

JsonParser::parse($source)->decoder(new JsonDecoder(decodesToArray: false));
```

The [simdjson extension](https://github.com/crazyxman/simdjson_php#simdjson_php) offers a decoder [faster](https://github.com/crazyxman/simdjson_php/tree/master/benchmark#run-phpbench-benchmark) than `json_decode()` that can be installed via `pecl install simdjson` if your server satisfies the [requirements](https://github.com/crazyxman/simdjson_php#requirement). JSON Parser leverages the simdjson decoder by default if the extension is loaded.

If we need a decoder that is not supported by default, we can implement our custom one.

<details><summary><b>Click here to see how to implement a custom decoder.</b></summary>

To create a custom decoder, we need to implement the `Decoder` interface and implement 1 method:

```php
use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Decoders\DecodedValue;

class CustomDecoder implements Decoder
{
    public function decode(string $json): DecodedValue
    {
        // return an instance of DecodedValue both in case of success or failure
    }
}
```

The method `decode()` defines the logic to decode the given JSON value and it needs to return an instance of `DecodedValue` both in case of success or failure.

To make custom decoder implementations even easier, JSON Parser provides an [abstract decoder](https://github.com/cerbero90/json-parser/tree/master/src/Decoders/AbstractDecoder.php) that hydrates `DecodedValue` for us so that we just need to define how a JSON value should be decoded:

```php
use Cerbero\JsonParser\Decoders\AbstractDecoder;

class CustomDecoder extends AbstractDecoder
{
    protected function decodeJson(string $json): mixed
    {
        // decode the given JSON or throw an exception on failure
        return json_decode($json, flags: JSON_THROW_ON_ERROR);
    }
}
```

> ⚠️ Please make sure to throw an exception in `decodeJson()` if the decoding process fails.

Now that we have implemented our custom decoder, we can set it like this:

```php
JsonParser::parse($source)->decoder(new CustomDecoder());
```

To see some implementation examples, please refer to the [already existing decoders](https://github.com/cerbero90/json-parser/tree/master/src/Decoders).

If you find yourself implementing the same custom decoder in different projects, feel free to send a PR and we will consider to support your custom decoder by default. Thank you in advance for any contribution!
</details>


### 💢 Errors handling

Not all JSONs are valid, some may present syntax errors due to an incorrect structure (e.g. `[}`) or decoding errors when values can't be decoded properly (e.g. `[1a]`). JSON Parser allows us to intervene and define the logic to run when these issues occur:

```php
use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Exceptions\SyntaxException;

$json = JsonParser::parse($source)
    ->onSyntaxError(fn (SyntaxException $e) => $this->handleSyntaxError($e))
    ->onDecodingError(fn (DecodedValue $decoded) => $this->handleDecodingError($decoded));
```

We can even replace invalid values with placeholders to avoid that the entire JSON parsing fails because of them:

```php
// instead of failing, replace invalid values with NULL
$json = JsonParser::parse($source)->patchDecodingError();

// instead of failing, replace invalid values with '<invalid>'
$json = JsonParser::parse($source)->patchDecodingError('<invalid>');
```

For more advanced decoding errors patching, we can pass a closure that has access to the `DecodedValue` instance:

```php
use Cerbero\JsonParser\Decoders\DecodedValue;

$patches = ['1a' => 1, '2b' => 2];
$json = JsonParser::parse($source)
    ->patchDecodingError(fn (DecodedValue $decoded) => $patches[$decoded->json] ?? null);
```

Any exception thrown by this package implements the `JsonParserException` interface. This makes it easy to handle all exceptions in a single catch block:

```php
use Cerbero\JsonParser\Exceptions\JsonParserException;

try {
    JsonParser::parse($source)->traverse();
} catch (JsonParserException) {
    // handle any exception thrown by JSON Parser
}
```

For reference, here is a comprehensive table of all the exceptions thrown by this package:
|`Cerbero\JsonParser\Exceptions\`|thrown when|
|---|---|
|`DecodingException`|a value in the JSON can't be decoded|
|`GuzzleRequiredException`|Guzzle is not installed and the JSON source is an endpoint|
|`IntersectingPointersException`|two JSON pointers intersect|
|`InvalidPointerException`|a JSON pointer syntax is not valid|
|`SyntaxException`|the JSON structure is not valid|
|`UnsupportedSourceException`|a JSON source is not supported|


### ⏳ Progress

When processing large JSONs, it can be helpful to track the parsing progress. JSON Parser provides convenient methods for accessing all the progress details:

```php
$json = new JsonParser($source);

$json->progress(); // <Cerbero\JsonParser\ValueObjects\Progress>
$json->progress()->current(); // the already parsed bytes e.g. 86759341
$json->progress()->total(); // the total bytes to parse e.g. 182332642
$json->progress()->fraction(); // the completed fraction e.g. 0.47583
$json->progress()->percentage(); // the completed percentage e.g. 47.583
$json->progress()->format(); // the formatted progress e.g. 47.5%
```

The total size of a JSON is calculated differently depending on the [source](#-sources). In some cases, it may not be possible to determine the size of a JSON and only the current progress is known:

```php
$json->progress()->current(); // 86759341
$json->progress()->total(); // null
$json->progress()->fraction(); // null
$json->progress()->percentage(); // null
$json->progress()->format(); // null
```


### 🛠 Settings

JSON Parser also provides other settings to fine-tune the parsing process. For example we can set the number of bytes to read when parsing JSON strings or streams:

```php
$json = JsonParser::parse($source)->bytes(1024 * 16); // read JSON chunks of 16KB
```

## 📆 Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🧪 Testing

``` bash
composer test
```

## 💞 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## 🧯 Security

If you discover any security related issues, please email andrea.marco.sartori@gmail.com instead of using the issue tracker.

## 🏅 Credits

- [Andrea Marco Sartori][link-author]
- [All Contributors][link-contributors]

## ⚖️ License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-author]: https://img.shields.io/static/v1?label=author&message=cerbero90&color=50ABF1&logo=twitter&style=flat-square
[ico-php]: https://img.shields.io/packagist/php-v/cerbero/json-parser?color=%234F5B93&logo=php&style=flat-square
[ico-version]: https://img.shields.io/packagist/v/cerbero/json-parser.svg?label=version&style=flat-square
[ico-actions]: https://img.shields.io/github/actions/workflow/status/cerbero90/json-parser/build.yml?branch=master&style=flat-square&logo=github
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-psr7]: https://img.shields.io/static/v1?label=compliance&message=PSR-7&color=blue&style=flat-square
[ico-psr12]: https://img.shields.io/static/v1?label=compliance&message=PSR-12&color=blue&style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/json-parser.svg?style=flat-square&logo=scrutinizer
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/json-parser.svg?style=flat-square&logo=scrutinizer
[ico-phpstan]: https://img.shields.io/badge/level-max-success?style=flat-square&logo=data:image/png;base64,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
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/json-parser.svg?style=flat-square

[link-author]: https://twitter.com/cerbero90
[link-php]: https://www.php.net
[link-packagist]: https://packagist.org/packages/cerbero/json-parser
[link-actions]: https://github.com/cerbero90/json-parser/actions?query=workflow%3Abuild
[link-psr7]: https://www.php-fig.org/psr/psr-7/
[link-psr12]: https://www.php-fig.org/psr/psr-12/
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/json-parser/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/json-parser
[link-phpstan]: https://phpstan.org/
[link-downloads]: https://packagist.org/packages/cerbero/json-parser
[link-contributors]: ../../contributors
