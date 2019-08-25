
<p align="center">
    <img src="resources/images/sample-code.png?raw=true">
</p>



# Laravel transformer

transform array keys easly.

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

you can transform array keys by passing format into `get($format)` method

```php
$originalData = [
    'f_name' => 'mahdi',
    'l_name' => 'khanzadi'
];

$role = [
    'f_name' => 'first_name',
    'l_name' => 'last_name',
];

$transformedData = (new Transform($originalData))->get($role);

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

$transform = new Transform;

$data = $transform
    ->setOriginalData($originalData)
    ->from(['f_name', 'l_name'])
    ->to(['first_name', 'last_name'])
    ->get();
/*
data:
[
    'first_name' => 'mahdi',
    'last_name' => 'khanzadi'
]
*/ 
```

as you see, you can set original data dynamically using `setOriginalData` method.

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
