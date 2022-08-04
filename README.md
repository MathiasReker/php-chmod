<h1 align="center">Php File Permissions</h1>

`PHP File Permissions` is a PHP library for extended work with file permissions.

[![Packagist Version](https://img.shields.io/packagist/v/MathiasReker/php-file-permissions.svg)](https://packagist.org/packages/MathiasReker/php-file-permissions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/MathiasReker/php-file-permissions.svg?color=%23ff007f)](https://packagist.org/packages/MathiasReker/php-file-permissions)
[![CI status](https://github.com/MathiasReker/php-file-permissions/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/MathiasReker/php-file-permissions/actions/workflows/ci.yml)
[![Contributors](https://img.shields.io/github/contributors/MathiasReker/blmvuln.svg)](https://github.com/MathiasReker/php-file-permissions/graphs/contributors)
[![Forks](https://img.shields.io/github/forks/MathiasReker/php-file-permissions.svg)](https://github.com/MathiasReker/php-file-permissions/network/members)
[![Stargazers](https://img.shields.io/github/stars/MathiasReker/php-file-permissions.svg)](https://github.com/MathiasReker/php-file-permissions/stargazers)
[![Issues](https://img.shields.io/github/issues/MathiasReker/php-file-permissions.svg)](https://github.com/MathiasReker/php-file-permissions/issues)
[![MIT License](https://img.shields.io/github/license/MathiasReker/php-file-permissions.svg)](https://github.com/MathiasReker/php-file-permissions/blob/develop/LICENSE.txt)

### Versions & Dependencies

| Version | PHP  | Documentation |
|---------|------|---------------|
| ^1.0    | ^7.4 | current       |

### Requirements

- `PHP` >= 7.4
- php-extension `ext-mbstring`

### Installation

`composer require MathiasReker/php-file-permissions`

Latest stable
version: [![Latest Stable Version](https://poser.pugx.org/MathiasReker/php-file-permissions/v/stable)](https://packagist.org/packages/MathiasReker/php-file-permissions)

### Examples

#### Dry run:

```php
<?php

use MathiasReker\FilePerm;

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

#### Fix:

```php
<?php

use MathiasReker\FilePerm;

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

`dryRun` run returns an array of concerned files/folders:

```php
$result->dryRun();
```

`fix` changes the concerned files/folders permission to the default mode:

```php
$result->fix();
```

`exclude` excludes a list of files/folder names (not paths):

```php
$result->exclude(['.docker']);
```
