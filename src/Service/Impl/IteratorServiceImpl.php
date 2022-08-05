<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service\Impl;

use Closure;
use FilesystemIterator;
use MathiasReker\PhpChmod\Model\Iterator;
use MathiasReker\PhpChmod\Service\IteratorService;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class IteratorServiceImpl implements IteratorService
{
    private Iterator $iterator;

    public function __construct()
    {
        $this->iterator = new Iterator();
    }

    public function setDirectory(string $directory): self
    {
        $this->iterator->setDirectory($directory);

        return $this;
    }

    public function setExcludedNames(array $excludedNames): self
    {
        $this->iterator->setExcludedDirectories($excludedNames);

        return $this;
    }

    public function getPaths(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($this->iterator->getDirectory(), FilesystemIterator::SKIP_DOTS),
                $this->getClosure()
            ),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
    }

    private function getClosure(): Closure
    {
        return fn ($path) => !\in_array($path->getFilename(), $this->iterator->getExcludedDirectories(), true);
    }
}
