# File Permissions library for PHP

## Example of usage:

### Dry run:

```php
<?php

use MathiasReker\Filepermissions\FilePermissions;

require __DIR__ . '/vendor/autoload.php';

$result = (new FilePermissions([__DIR__]))
    ->setDefaultModeFile(0644)
    ->setDefaultModeFolder(0755)
    ->setAllowedModeFiles([0400, 0444, 0640])
    ->setAllowedModeFolders([0750])
    ->scan()
    ->dryRun();

var_dump($result);
```

### Fix:

```php
<?php

use MathiasReker\Filepermissions\FilePermissions;

require __DIR__ . '/vendor/autoload.php';

(new FilePermissions([__DIR__]))
    ->setDefaultModeFile(0644)
    ->setDefaultModeFolder(0755)
    ->setAllowedModeFiles([0400, 0444, 0640])
    ->setAllowedModeFolders([0750])
    ->scan()
    ->fix();
```

## Documentation

TODO
