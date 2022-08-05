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

use MathiasReker\PhpChmod\FilePerm;

require __DIR__ . '/vendor/autoload.php';

$result = (new FilePerm([__DIR__]))
    ->setDefaultModeFile(0644)
    ->setDefaultModeFolder(0755)
    ->setAllowedModeFiles([0400, 0444, 0640])
    ->setAllowedModeFolders([0750])
    ->scan()
    ->dryRun();

var_dump($result); // string[]
```

Fix:

```php
<?php

use MathiasReker\PhpChmod\FilePerm;

require __DIR__ . '/vendor/autoload.php';

$result = (new FilePerm([__DIR__]))
    ->setDefaultModeFile(0644)
    ->setDefaultModeFolder(0755)
    ->setAllowedModeFiles([0400, 0444, 0640])
    ->setAllowedModeFolders([0750])
    ->scan()
    ->fix();

var_dump($result); // bool
```

### Documentation

The constructor takes an array of directories to scan:

```php
$result = new FilePerm([__DIR__]);
```

`setDefaultModeFile` sets the default file permission:

```php
$result->setDefaultModeFile(0644);
```

`setDefaultModeFolder` sets the default folder permission:

```php
$result->setDefaultModeFolder(0755);
```

`setAllowedModeFiles` sets the allowed permissions for files. Files with these permissions will be skipped:

```php
$result->setAllowedModeFiles([0400, 0444, 0640]);
```

`setAllowedModeFolders` sets the allowed permissions for folders. Folders with these permissions will be skipped:

```php
$result->setAllowedModeFolders([0750]);
```

`scan` finds all the concerned files/folders:

```php
$result->scan();
```

`setConcernedPaths` sets concerned files manually. This is useful if you want to use a custom scanner:

```php
$result->setConcernedPaths($paths);
```

`dryRun` returns an array of concerned files/folders:

```php
$result->dryRun();
```

`fix` changes the concerned file/folder permissions to the default mode:

```php
$result->fix();
```

`excludedNames` excludes a list of files/folder names (not paths):

```php
$result->excludedNames(['.docker']);
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
