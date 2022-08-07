<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Model;

use MathiasReker\PhpChmod\Exception\InvalidArgumentException;

final class Scanner
{
    /**
     * @var string
     */
    private const INVALID_PERMISSION = 'Invalid permission.';

    private ?int $defaultFileModes = null;

    private ?int $defaultDirectoryModes = null;

    private array $getExcludedPaths = [];

    /**
     * @var int[]
     */
    private array $excludedFileModes = [];

    /**
     * @var int[]
     */
    private array $excludedDirectoryModes = [];

    /**
     * @var string[]
     */
    private array $concernedPaths = [];

    /**
     * @var string[]
     */
    private array $excludedNames = [];

    /**
     * @var string[]
     */
    private array $names = [];

    public function setDefaultFileMode(int $defaultFileModes): self
    {
        if (!$this->isValidMode($defaultFileModes)) {
            throw new InvalidArgumentException(self::INVALID_PERMISSION);
        }

        $this->defaultFileModes = $defaultFileModes;

        return $this;
    }

    public function setDefaultDirectoryMode(int $defaultDirectoryModes): self
    {
        if (!$this->isValidMode($defaultDirectoryModes)) {
            throw new InvalidArgumentException(self::INVALID_PERMISSION);
        }

        $this->defaultDirectoryModes = $defaultDirectoryModes;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getExcludedFileModes(): array
    {
        return $this->excludedFileModes;
    }

    /**
     * @param string[] $getExcludedPaths
     */
    public function setExcludedPaths(array $getExcludedPaths): self
    {
        $this->getExcludedPaths = $getExcludedPaths;

        return $this;
    }

    /**
     * @param int[] $excludedFileModes
     */
    public function setExcludedFileModes(
        array $excludedFileModes
    ): self {
        foreach ($excludedFileModes as $excludedFileMode) {
            if (!$this->isValidMode($excludedFileMode)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->excludedFileModes = $excludedFileModes;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getExcludedDirectoryModes(): array
    {
        return $this->excludedDirectoryModes;
    }

    /**
     * @param int[] $excludedDirectoryModes
     */
    public function setExcludedDirectoryModes(
        array $excludedDirectoryModes
    ): self {
        foreach ($excludedDirectoryModes as $excludedDirectoryMode) {
            if (!$this->isValidMode($excludedDirectoryMode)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->excludedDirectoryModes = $excludedDirectoryModes;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getConcernedPaths(): array
    {
        return $this->concernedPaths;
    }

    /**
     * @param string[] $concernedPaths
     */
    public function addConcernedPaths(array $concernedPaths): self
    {
        $this->concernedPaths += $concernedPaths;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedNames(): array
    {
        return $this->excludedNames;
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param string[] $names
     */
    public function setNames(array $names): self
    {
        $this->names = $names;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedPaths(): array
    {
        return $this->getExcludedPaths;
    }

    /**
     * @param string[] $excludedNames
     */
    public function setExcludeNames(array $excludedNames): self
    {
        $this->excludedNames = $excludedNames;

        return $this;
    }

    public function getDefaultFileModes(): ?int
    {
        return $this->defaultFileModes;
    }

    public function getDefaultDirectoryModes(): ?int
    {
        return $this->defaultDirectoryModes;
    }

    private function isValidMode(int $mode): bool
    {
        return \in_array(mb_strlen(decoct($mode)), [3, 4], true);
    }
}
