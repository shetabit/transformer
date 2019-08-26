
<p align="center">
    <img src="resources/images/sample-code.png?raw=true">
</p>



# Laravel transformer

transform array keys easily.

## List of contents

- [Install](#install)
- [How to use](#how-to-use)
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

you can transform array keys using `get($transformer)` method

```php
$originalData = [
    'f_name' => 'mahdi',
    'l_name' => 'khanzadi'
];

$role = [
    'f_name' => 'first_name',
    'l_name' => 'last_name',
];

$transformer = new Transformer($role);

$transformedData = (new Transform($originalData))->get($transformer);

/*
data:
[
    'first_name' => 'mahdi',
    'last_name' => 'khanzadi'
]
*/
```

or you can use `from($currentFormat)` and `to($destinationFormat)`.

```php
$originalData = [
    'f_name' => 'mahdi',
    'l_name' => 'khanzadi'
];

$transformer = new Transformer();

// convert f_name to first_name
$transformer->from('f_name')->to('first_name');

// convert l_name to last_name
$transformer->from('l_name')->to('last_name');

$transformedData = (new Transform($originalData))->use($transformer)->get();

/*
data:
[
    'first_name' => 'mahdi',
    'last_name' => 'khanzadi'
]
*/
```

as you see, you can set original data dynamically using `setOriginalData` method.

## Transformers

you can use `Shetabit\Transform\Classes\Transformer` to transform array keys.

if you have complex array structures, you can create a custom transformer by implementing `TransformerInterface`

```php
// at the top
use Shetabit\Transformer\Contracts\TransformerInterface;

// ...

class CustomTranformerName implements TransformerInterface
{
    /**
     * Transform data
     *
     * @return array
     */
    public function transform(array $data) : array
    {
        return [
            'user_name' => $data['u'],
            'password' => $data['p']
        ];
    }
}

// use your custom transformer 
$originalData = [
    'u' => 'mahdikhanzadi',
    'p' => '246810'
];

$transformer = new CustomTranformerName();

$transformedData = (new Transform($originalData))->get($transformer);

/*
data:
[
    'username' => 'mahdikhanzadi',
    'password' => '246810'
]
*/
```

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

[link-packagist]: https://packagist.org/packages/shetabit/transformer
[link-code-quality]: https://scrutinizer-ci.com/g/shetabit/transformer
[link-author]: https://github.com/khanzadimahdi
[link-contributors]: ../../contributors
