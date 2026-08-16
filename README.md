<p align="center">
    <img src="resources/images/sample-code.png?raw=true">
</p>

# Transformer

transform array keys easily.

[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads on Packagist][ico-download]][link-packagist]
[![Tests][ico-tests]][link-tests]
[![Code Style][ico-code-style]][link-code-style]
[![Static Analysis][ico-static-analysis]][link-static-analysis]
[![Code Coverage][ico-coverage]][link-coverage]

This package supports `PHP 8.4+` and has no dependencies of its own, so it can be used in any PHP framework —
`Laravel`, `Symfony` and the rest — as well as in no framework at all.

## List of contents

- [Install](#install)
- [How to use](#how-to-use)
  - [Renaming with a format](#renaming-with-a-format)
  - [Renaming with from and to](#renaming-with-from-and-to)
  - [Nested arrays](#nested-arrays)
  - [How keys that collide are resolved](#how-keys-that-collide-are-resolved)
  - [Custom transformers](#custom-transformers)
- [Errors](#errors)
- [Testing](#testing)
- [Change log](#change-log)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

## Install

Via Composer

```bash
$ composer require shetabit/transformer
```

## How to use

### Renaming with a format

A format maps the keys the data has onto the keys it should have. Hand it to a `Transformer` and run it with
`get($transformer)`.

```php
use Shetabit\Transformer\Classes\Transform;
use Shetabit\Transformer\Classes\Transformer;

$originalData = [
    'f_name' => 'mahdi',
    'l_name' => 'khanzadi',
];

$format = [
    'f_name' => 'first_name',
    'l_name' => 'last_name',
];

$transformedData = new Transform($originalData)->get(new Transformer($format));

/*
[
    'first_name' => 'mahdi',
    'last_name' => 'khanzadi',
]
*/
```

A key the format does not mention is left as it is, and every key keeps the place it had in the data.

The original data can be set at any time with `setOriginalData()`, so one `Transform` can be run over row after row:

```php
$transform = new Transform()->use(new Transformer($format));

foreach ($rows as $row) {
    $transformed[] = $transform->setOriginalData($row)->get();
}
```

### Renaming with from and to

The same format can be written down pair by pair with `from($currentFormat)` and `to($destinationFormat)`.

```php
$transformer = new Transformer();

$transformer->from('f_name')->to('first_name');
$transformer->from('l_name')->to('last_name');

$transformedData = new Transform($originalData)->use($transformer)->get();
```

Both take a list as well:

```php
$transformer = new Transformer()
    ->from(['f_name', 'l_name'])
    ->to(['first_name', 'last_name']);
```

### Nested arrays

By default only the keys of the array itself are renamed. A recursive transformer applies the same format to every
array nested in the data, including the arrays of a list:

```php
$transformer = new Transformer([
    'cust' => 'customer',
    'f_name' => 'first_name',
    'itms' => 'items',
    'sku' => 'code',
], recursive: true);

$transformedData = new Transform([
    'cust' => ['f_name' => 'mahdi'],
    'itms' => [['sku' => 'A-1'], ['sku' => 'B-7']],
])->get($transformer);

/*
[
    'customer' => ['first_name' => 'mahdi'],
    'items' => [['code' => 'A-1'], ['code' => 'B-7']],
]
*/
```

`recursive()` turns it on for a transformer that was built without it, `recursive(false)` turns it off again.

### How keys that collide are resolved

Every key is renamed at once, so a key can take the name another key is giving up in the same run. Two columns that
were filled in the wrong order are swapped back with:

```php
new Transformer(['first_name' => 'last_name', 'last_name' => 'first_name']);
```

Where two keys would end up with the same name the renamed one wins, and of two renamed ones the one that comes first
in the data:

```php
new Transformer(['legacy_total' => 'total'])->transform(['legacy_total' => 12900, 'total' => 0]);

// ['total' => 12900]
```

### Custom transformers

For a structure a format cannot describe, write a transformer of your own by implementing `TransformerInterface`.

```php
use Shetabit\Transformer\Contracts\TransformerInterface;

class CredentialsTransformer implements TransformerInterface
{
    public function transform(array $data) : array
    {
        return [
            'username' => $data['u'],
            'password' => $data['p'],
        ];
    }
}

$transformedData = new Transform(['u' => 'mahdikhanzadi', 'p' => '246810'])
    ->get(new CredentialsTransformer());

/*
[
    'username' => 'mahdikhanzadi',
    'password' => '246810',
]
*/
```

## Errors

Every exception of the package extends `Shetabit\Transformer\Exceptions\TransformerException`, so all of them can be
caught at once.

| Exception | Thrown when |
| --- | --- |
| `TransformerNotValidException` | `Transform::get()` is run without a transformer. |
| `InvalidFormatException` | `from()` and `to()` were not given the same number of keys. |

## Testing

Every pull request and every push to `master` is checked by [GitHub Actions][link-actions]: the test suite runs on
PHP 8.4 and 8.5 (against both the lowest and the highest supported dependencies), the coding style is checked with
PHP_CodeSniffer, the sources are analysed with PHPStan and the code coverage of the test suite is measured and has to
stay above 95%.

Next to the unit tests there are feature tests that run realistic nested payloads through the package end to end: a
whole tree renamed and renamed back with the flipped format, rows whose columns are swapped or shifted along, and the
output of one transformer handed to the next.

You can run the same checks locally. With PHP and Composer installed on your machine:

```bash
composer install

composer test           # run the test suite
composer test-coverage  # run the test suite and report code coverage
composer check-style    # check the coding style
composer fix-style      # fix the coding style where possible
composer analyse        # run static analysis
composer ci             # run all of the checks above
```

If you would rather not install PHP on your machine, the shipped `Dockerfile` and `Makefile` run everything inside a
container:

```bash
make test              # run the test suite
make coverage          # run the test suite and report code coverage
make check-style       # check the coding style
make fix-style         # fix the coding style where possible
make analyse           # run static analysis
make ci                # run all of the checks above
make shell             # open a shell inside the container
make help              # list every available target
```

Another PHP version can be used with `make test PHP_VERSION=8.5`.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email khanzadimahdi@gmail.com instead of using the issue tracker.

## Credits

- [Mahdi khanzadi][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/shetabit/transformer.svg?style=flat-square
[ico-download]: https://img.shields.io/packagist/dt/shetabit/transformer.svg?color=%23F18&style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-tests]: https://img.shields.io/github/actions/workflow/status/shetabit/transformer/tests.yml?branch=master&label=Tests&style=flat-square
[ico-code-style]: https://img.shields.io/github/actions/workflow/status/shetabit/transformer/code-style.yml?branch=master&label=Code%20Style&style=flat-square
[ico-static-analysis]: https://img.shields.io/github/actions/workflow/status/shetabit/transformer/static-analysis.yml?branch=master&label=Static%20Analysis&style=flat-square
[ico-coverage]: https://img.shields.io/codecov/c/github/shetabit/transformer/master?label=Coverage&style=flat-square

[link-packagist]: https://packagist.org/packages/shetabit/transformer
[link-actions]: https://github.com/shetabit/transformer/actions
[link-tests]: https://github.com/shetabit/transformer/actions/workflows/tests.yml
[link-code-style]: https://github.com/shetabit/transformer/actions/workflows/code-style.yml
[link-static-analysis]: https://github.com/shetabit/transformer/actions/workflows/static-analysis.yml
[link-coverage]: https://codecov.io/gh/shetabit/transformer
[link-author]: https://github.com/khanzadimahdi
[link-contributors]: ../../contributors
