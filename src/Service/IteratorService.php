<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service;

interface IteratorService
{
    public function setExcludedNames(array $excludedNames);

    public function setDirectory(string $directory);

    public function getPaths();
}
