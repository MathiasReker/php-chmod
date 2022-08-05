<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Util;

final class OperatingSystem
{
    public static function isWindows(): bool
    {
        return 'WIN' === mb_strtoupper(
            mb_substr(\PHP_OS, 0, 3)
        );
    }
}
