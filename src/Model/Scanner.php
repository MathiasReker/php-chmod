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

    private ?int $defaultModeFiles = null;

    private ?int $defaultModeFolders = null;

    /**
     * @var int[]
     */
    private array $allowedModeFiles = [];

    /**
     * @var int[]
     */
    private array $allowedModeFolders = [];

    /**
     * @var string[]
     */
    private array $concernedPaths = [];

    /**
     * @var string[]
     */
    private array $excludedNames = [];

    public function setDefaultModeFile(int $defaultModeFiles): self
    {
        if (!$this->isValidMode($defaultModeFiles)) {
            throw new InvalidArgumentException(self::INVALID_PERMISSION);
        }

        $this->defaultModeFiles = $defaultModeFiles;

        return $this;
    }

    public function setDefaultModeFolder(int $defaultModeFolders): self
    {
        if (!$this->isValidMode($defaultModeFolders)) {
            throw new InvalidArgumentException(self::INVALID_PERMISSION);
        }

        $this->defaultModeFolders = $defaultModeFolders;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAllowedModeFiles(): array
    {
        return $this->allowedModeFiles;
    }

    /**
     * @param int[] $allowedModeFiles
     */
    public function setAllowedModeFiles(array $allowedModeFiles): self
    {
        foreach ($allowedModeFiles as $allowedModeFile) {
            if (!$this->isValidMode($allowedModeFile)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->allowedModeFiles = $allowedModeFiles;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getAllowedModeFolders(): array
    {
        return $this->allowedModeFolders;
    }

    /**
     * @param int[] $allowedModeFolders
     */
    public function setAllowedModeFolders(
        array $allowedModeFolders
    ): self {
        foreach ($allowedModeFolders as $allowedModeFolder) {
            if (!$this->isValidMode($allowedModeFolder)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->allowedModeFolders = $allowedModeFolders;

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

    public function getDefaultModeFiles(): ?int
    {
        return $this->defaultModeFiles;
    }

    public function getDefaultModeFolders(): ?int
    {
        return $this->defaultModeFolders;
    }

    private function isValidMode(int $mode): bool
    {
        return \in_array(mb_strlen(decoct($mode)), [3, 4], true);
    }
}
