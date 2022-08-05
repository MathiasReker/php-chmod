<h1 align="center">PHP chmod</h1>

[![Packagist Version](https://img.shields.io/packagist/v/MathiasReker/php-chmod.svg)](https://packagist.org/packages/MathiasReker/php-chmod)
[![Packagist Downloads](https://img.shields.io/packagist/dt/MathiasReker/php-chmod.svg?color=%23ff007f)](https://packagist.org/packages/MathiasReker/php-chmod)
[![CI status](https://github.com/MathiasReker/php-chmod/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/MathiasReker/php-chmod/actions/workflows/ci.yml)
[![Contributors](https://img.shields.io/github/contributors/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/graphs/contributors)
[![Forks](https://img.shields.io/github/forks/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/network/members)
[![Stargazers](https://img.shields.io/github/stars/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/stargazers)
[![Issues](https://img.shields.io/github/issues/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/issues)
[![MIT License](https://img.shields.io/github/license/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/blob/develop/LICENSE.txt)

`php-chmod` is a PHP library for easily changing the permissions recursively.

### Versions & Dependencies

| Version | PHP  | Documentation |
|---------|------|---------------|
| ^1.0    | ^7.4 | current       |

### Requirements

- `PHP` >= 7.4
- php-extension `ext-mbstring`

### Installation

Run `composer require MathiasReker/php-chmod`

### Examples

Dry run:

```php
<?php

use MathiasReker\PhpChmod\Scanner;

require __DIR__ . '/vendor/autoload.php';

$result = (new Scanner())
    ->setDefaultFileMode(0644)
    ->setDefaultDirectoryMode(0755)
    ->setAllowedFileModes([0400, 0444, 0640])
    ->setAllowedDirectoryModes([0750])
    ->scan([__DIR__])
    ->dryRun();

var_dump($result); // string[]
```

Fix:

```php
<?php

use MathiasReker\PhpChmod\Scanner;

require __DIR__ . '/vendor/autoload.php';

$result = (new Scanner())
    ->setDefaultFileMode(0644)
    ->setDefaultDirectoryMode(0755)
    ->setAllowedFileModes([0400, 0444, 0640])
    ->setAllowedDirectoryModes([0750])
    ->scan([__DIR__])
    ->fix();

var_dump($result); // bool
```

### Documentation

```php
$result = new Scanner();
```

`setDefaultFileMode` sets the default file permission:

```php
$result->setDefaultFileMode(0644);
```

`setDefaultDirectoryMode` sets the default directory permission:

```php
$result->setDefaultDirectoryMode(0755);
```

`setAllowedFileModes` sets the allowed permissions for files. Files with these permissions will be skipped:

```php
$result->setAllowedFileModes([0400, 0444, 0640]);
```

`setAllowedDirectoryModes` sets the allowed permissions for directories. Directories with these permissions will be
skipped:

```php
$result->setAllowedDirectoryModes([0750]);
```

`excludedNames` excludes a list of files/directory names (not paths):

```php
$result->excludedNames(['.docker']);
```

`scan` finds all the concerned files/directories:

```php
$result->scan([__DIR__]);
```

`setConcernedPaths` sets concerned files manually. This is useful if you want to use a custom scanner:

```php
$result->setConcernedPaths($paths);
```

`dryRun` returns an array of concerned files/directories:

```php
$result->dryRun();
```

`fix` changes the concerned file/directory permissions to the default mode:

```php
$result->fix();
```

### Roadmap

See the [open issues](https://github.com/MathiasReker/php-chmod/issues) for a complete list of proposed
features (and known
issues).

### Contributing

If you have a suggestion to improve this, please fork the repo and create a pull request. You can also open an issue
with the tag "enhancement". Finally, don't forget to give the project a star! Thanks again!

### License

It is distributed under the MIT License. See `LICENSE` for more information.
