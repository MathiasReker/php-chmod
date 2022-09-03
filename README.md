<h1 align="center">PHP chmod</h1>

[![Packagist Version](https://img.shields.io/packagist/v/MathiasReker/php-chmod.svg)](https://packagist.org/packages/MathiasReker/php-chmod)
[![Packagist Downloads](https://img.shields.io/packagist/dt/MathiasReker/php-chmod.svg?color=%23ff007f)](https://packagist.org/packages/MathiasReker/php-chmod)
[![CI status](https://github.com/MathiasReker/php-chmod/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/MathiasReker/php-chmod/actions/workflows/ci.yml)
[![Contributors](https://img.shields.io/github/contributors/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/graphs/contributors)
[![Forks](https://img.shields.io/github/forks/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/network/members)
[![Stargazers](https://img.shields.io/github/stars/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/stargazers)
[![Issues](https://img.shields.io/github/issues/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/issues)
[![MIT License](https://img.shields.io/github/license/MathiasReker/php-chmod.svg)](https://github.com/MathiasReker/php-chmod/blob/develop/LICENSE.txt)

`php-chmod` is a PHP library for easily changing file/directory permissions recursively.

> ✅ Literal octal notation (0o) is supported

### Versions & Dependencies

| Version | PHP  | Documentation |
|---------|------|---------------|
| ^2.1    | ^7.4 | current       |

### Requirements

- `PHP` >= 7.4
- php-extension `ext-mbstring`

### Installation

Run `composer require mathiasreker/php-chmod`

### Examples

Dry run:

```php
<?php

use MathiasReker\PhpChmod\Scanner;

require __DIR__ . '/vendor/autoload.php';

$result = (new Scanner())
    ->setDefaultFileMode(0644)
    ->setDefaultDirectoryMode(0755)
    ->setExcludedFileModes([0400, 0444, 0640])
    ->setExcludedDirectoryModes([0750])
    ->scan([__DIR__])
    ->dryRun();

var_dump($result); // string[]
```

Fix:

```php
<?php

use MathiasReker\PhpChmod\Scanner;

require __DIR__ . '/vendor/autoload.php';

(new Scanner())
    ->setDefaultFileMode(0644)
    ->setDefaultDirectoryMode(0755)
    ->setExcludedFileModes([0400, 0444, 0640])
    ->setExcludedDirectoryModes([0750])
    ->scan([__DIR__])
    ->fix(); // void
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

`setExcludedFileModes` sets the allowed permissions for files. Files with these permissions will be skipped:

```php
$result->setExcludedFileModes([0400, 0444, 0640]);
```

`setExcludedDirectoryModes` sets the allowed permissions for directories. Directories with these permissions will be
skipped:

```php
$result->setExcludedDirectoryModes([0750]);
```

`setExcludedNames` exclude files by a custom pattern. Glob and RegEx are supported:

```php
$result->setExcludedNames(['*.rb', '*.py']);
```

`setNames` includes files by a custom pattern and exclude any other files. Glob and RegEx are supported:

```php
$result->setNames(['*.php']);
```

`setExcludedPaths` excludes a list of file/directory paths:

```php
$result->setExcludedPaths(['first/dir', 'other/dir']);
```

`doExcludeFiles` excludes all files:

```php
$result->doExcludeFiles();
```

`doExcludeDirectories` excludes all directories:

```php
$result->doExcludeDirectories();
```

`scan` finds all the concerned files/directories:

```php
$result->scan([__DIR__]);
```

`setPaths` sets paths of files/directories manually. This is an alternative to the scanner if you want to use a custom
scanner:

```php
$result->setPaths($paths);
```

`dryRun` returns an array of the concerned files/directories:

```php
$result->dryRun();
```

`fix` changes the concerned files/directories permissions to the default permission:

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

#### Docker

If you are using docker, you can use the following command to get started:

```bash
docker-compose up -d
```

Next, access the container:

```bash
docker exec -it php-chmod bash
```

#### Tools

PHP Coding Standards Fixer:

```bash
composer run-script cs-fix
```

PHP Coding Standards Checker:

```bash
composer run-script cs-check
```

PHP Stan:

```bash
composer run-script phpstan
```

Unit tests:

```bash
composer run-script test
```

### License

It is distributed under the MIT License. See `LICENSE` for more information.
