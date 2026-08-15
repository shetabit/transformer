# Changelog

All Notable changes to `shetabit/transformer` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

This is the next major version. It requires PHP 8.4 and it changes what the package does with keys that collide, so
it is not a drop-in replacement for 2.x. `shetabit/transform-request` requires `^2.0` today and has to widen that
constraint before it can be used with this release.

### Added
- **Recursive renaming.** A transformer built with `recursive: true` — or told `recursive()` afterwards — applies its
  format to every array nested in the data, the arrays of a list included. Off by default, so a flat transformation
  behaves the way it always did.
- `Transformer::getFormat()` is public, so the pairs a transformer was built out of can be read back.
- `Transformer::isRecursive()` and `Transform::getTransformer()`.
- `Shetabit\Transformer\Exceptions\TransformerException`, which every exception of the package extends, and
  `Shetabit\Transformer\Exceptions\InvalidFormatException`.
- A test suite of 66 tests. Next to the unit tests there are feature tests that run realistic nested payloads through
  the package end to end: a whole tree renamed and renamed back with the flipped format, rows whose columns are
  swapped or shifted along, and the output of one transformer handed to the next.
- GitHub Actions workflows running the test suite (PHP 8.4 and 8.5, lowest and highest dependencies), the coding style
  check, the static analysis and the code coverage on every pull request and on every push to `master`. The coverage
  has to stay above 95%.
- A `Dockerfile` (with pcov, for coverage) and a `Makefile` to run the test suite and every check inside a container.
- A `phpcs.xml.dist` ruleset, a `phpstan.neon.dist` (level 7, no baseline) and a `rector.php`.
- The `analyse`, `rector`, `test-coverage` and `ci` composer scripts.

### Changed
- **Breaking:** PHP 8.4 is now the minimum required version, and `php: ^8.4` is required explicitly — `composer.json`
  had no `require` section at all.
- **Breaking:** every key is renamed at once instead of one pair of the format after the other. Where two keys end up
  with the same name the renamed one wins, and of two renamed ones the one that comes first in the data.
- **Breaking:** `Transformer::from()` and `Transformer::to()` declare `int|string|array`, and `Transform` returns
  `static` from its setters. An override in a subclass has to declare compatible types.
- **Breaking:** a format whose `from()` and `to()` do not hold the same number of keys throws an
  `InvalidFormatException` instead of the `ValueError` of `array_combine()`.
- The package was modernized for PHP 8.4. Every parameter, return value and property of `src/` declares a type now,
  and `new Foo()->bar()` replaces `(new Foo())->bar()`. Nullable types are spelled `T|null`.
- `TransformerNotValidException` extends `TransformerException` instead of `\Exception` directly.
- The development dependencies were updated: PHPUnit 8.3 to 11.5/12/13, PHP_CodeSniffer 3.4 to 4 (through
  `phpcsstandards/php_codesniffer`, not `squizlabs/php_codesniffer`), and PHPStan 2.2 and Rector 2.6 were added.
- `phpunit.xml` was migrated to the current PHPUnit schema and made strict about risky tests, warnings, notices and
  deprecations. The suite is split into a `Unit` and a `Feature` testsuite.
- `composer.lock` is no longer committed. A library should resolve against whatever the application it is installed
  into already has.

### Removed
- **Breaking:** the global `array_splice_assoc()` function and the `src/Functions/array_slice_assoc.php` file it lived
  in, along with the `autoload.files` entry that pulled it into every process. Nothing needs it any more.
- **Breaking:** `Transformer::replaceKey()`, the protected method that renamed one key at a time.
- The Travis CI configuration (`.travis.yml`), replaced by GitHub Actions.
- The StyleCI configuration (`.styleci.yml`), replaced by the PHP_CodeSniffer workflow.
- The `branch-alias` of `composer.json`.

### Fixed
- **Two keys can trade names.** The format was applied pair by pair over the result of the pair before it, so
  `['a' => 'b', 'b' => 'a']` renamed `a` to `b` and then renamed that same `b` back to `a`: `['a' => 1, 'b' => 2]`
  came out as `['a' => 2]` and the value of `a` was gone. Renaming a chain (`['a' => 'b', 'b' => 'c']`) carried the
  value of `a` all the way to `c` for the same reason.
- **A renamed key no longer loses its value to a key that already carries the destination name.** The renamed key was
  spliced in ahead of the untouched one and `array_merge()` then let the untouched one win, so
  `['legacy_total' => 12900, 'total' => 0]` renamed with `['legacy_total' => 'total']` came out as `['total' => 0]`.
- **Integer keys survive a transformation.** The splice was built with `array_merge()`, which renumbers integer keys,
  so renaming a single key of `[10 => 'ten', 'ord_id' => 4711, 20 => 'twenty']` renumbered `10` and `20` to `0` and
  `1`.
- **The guard of the shipped function matched the function it guarded.** `src/Functions/array_slice_assoc.php` asked
  `function_exists('array_slice_assoc')` before declaring `array_splice_assoc()` — two different names. Anything else
  in the process declaring `array_slice_assoc()` left `array_splice_assoc()` undeclared and every transformation died
  with a fatal error, while a second declaration of `array_splice_assoc()` itself went unnoticed. The file is gone.
- **`Transform::get()` does not warn on PHP 8.4.** Its argument was written `TransformerInterface $transformer = null`,
  an implicit nullable, which PHP 8.4 deprecates. It is `TransformerInterface|null` now.
- The readme called the package "Laravel transformer" and the changelog called it "extractor". It is neither: it is
  `shetabit/transformer` and it needs no framework.

## Date - 2019-01-09

### Fixed
- Nothing

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing
