<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Exception;

use RuntimeException;

/**
 * Thrown to indicate that a method has been passed an illegal or
 * inappropriate argument.
 */
final class InvalidArgumentException extends RuntimeException
{
}
