<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Model;

final class Iterator
{
    private string $directory;

    /**
     * @var string[]
     */
    private array $excludedDirectories = [];

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }

    /**
     * @param string[] $excludedDirectories
     */
    public function setExcludedDirectories(
        array $excludedDirectories
    ): self {
        $this->excludedDirectories = $excludedDirectories;

        return $this;
    }
}
