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

    /**
     * @var int[]
     */
    private array $allowedFileModes = [];

    /**
     * @var int[]
     */
    private array $allowedDirectoryModes = [];

    /**
     * @var string[]
     */
    private array $concernedPaths = [];

    /**
     * @var string[]
     */
    private array $excludedNames = [];

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
    public function getAllowedFileModes(): array
    {
        return $this->allowedFileModes;
    }

    /**
     * @param int[] $allowedFileModes
     */
    public function setAllowedFileModes(array $allowedFileModes): self
    {
        foreach ($allowedFileModes as $allowedModeFile) {
            if (!$this->isValidMode($allowedModeFile)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->allowedFileModes = $allowedFileModes;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAllowedDirectoryModes(): array
    {
        return $this->allowedDirectoryModes;
    }

    /**
     * @param int[] $allowedDirectoryModes
     */
    public function setAllowedDirectoryModes(
        array $allowedDirectoryModes
    ): self {
        foreach ($allowedDirectoryModes as $allowedDirectoryMode) {
            if (!$this->isValidMode($allowedDirectoryMode)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->allowedDirectoryModes = $allowedDirectoryModes;

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
