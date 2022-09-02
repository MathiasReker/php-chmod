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

    private int $defaultFileMode = 0644;

    private int $defaultDirectoryMode = 0755;

    private bool $isExcludedFiles = false;

    private bool $isExcludedDirectories = false;

    /**
     * @var string[]
     */
    private array $excludedPaths = [];

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
    private array $paths = [];

    /**
     * @var string[]
     */
    private array $excludedNames = [];

    /**
     * @var string[]
     */
    private array $names = [];

    /**
     * Get excluded permissions for files.
     *
     * @return int[]
     */
    public function getExcludedFileModes(): array
    {
        return $this->excludedFileModes;
    }

    /**
     * Set excluded permissions for files.
     *
     * @param int[] $excludedFileModes
     */
    public function setExcludedFileModes(array $excludedFileModes): self
    {
        foreach ($excludedFileModes as $excludedFileMode) {
            if (!$this->isValidMode($excludedFileMode)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->excludedFileModes = $excludedFileModes;

        return $this;
    }

    /**
     * Check if the permission mode is valid.
     */
    private function isValidMode(int $mode): bool
    {
        return \in_array(mb_strlen(decoct($mode)), [3, 4], true);
    }

    /**
     * Get excluded permissions for directories.
     *
     * @return int[]
     */
    public function getExcludedDirectoryModes(): array
    {
        return $this->excludedDirectoryModes;
    }

    /**
     * Set excluded permissions for directories.
     *
     * @param int[] $excludedDirectoryModes
     */
    public function setExcludedDirectoryModes(array $excludedDirectoryModes): self
    {
        foreach ($excludedDirectoryModes as $excludedDirectoryMode) {
            if (!$this->isValidMode($excludedDirectoryMode)) {
                throw new InvalidArgumentException(self::INVALID_PERMISSION);
            }
        }

        $this->excludedDirectoryModes = $excludedDirectoryModes;

        return $this;
    }

    /**
     * Get paths matching the search pattern.
     *
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Set paths.
     *
     * @param string[] $paths
     */
    public function setPaths(array $paths): self
    {
        $this->paths += $paths;

        return $this;
    }

    /**
     * Get names that directories/files must not match.
     *
     * @return string[]
     */
    public function getExcludedNames(): array
    {
        return $this->excludedNames;
    }

    /**
     * Get names that directories/files must match.
     *
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * Set names the directories/files must match.
     *
     * @param string[] $names
     */
    public function setNames(array $names): self
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get paths that directories/files must not match.
     *
     * @return string[]
     */
    public function getExcludedPaths(): array
    {
        return $this->excludedPaths;
    }

    /**
     * Set excluded paths.
     *
     * @param string[] $excludedPaths
     */
    public function setExcludedPaths(array $excludedPaths): self
    {
        $this->excludedPaths = $excludedPaths;

        return $this;
    }

    /**
     * Set names that directories/files must match.
     *
     * @param string[] $excludedNames
     */
    public function setExcludeNames(array $excludedNames): self
    {
        $this->excludedNames = $excludedNames;

        return $this;
    }

    /**
     * Get names that directories/files must not match.
     */
    public function getDefaultFileMode(): int
    {
        return $this->defaultFileMode;
    }

    /**
     * Set the default permission for files.
     */
    public function setDefaultFileMode(int $defaultFileMode): self
    {
        if (!$this->isValidMode($defaultFileMode)) {
            throw new InvalidArgumentException(self::INVALID_PERMISSION);
        }

        $this->defaultFileMode = $defaultFileMode;

        return $this;
    }

    /**
     * Get default directories mode.
     */
    public function getDefaultDirectoryMode(): int
    {
        return $this->defaultDirectoryMode;
    }

    /**
     * Set the default permission for directories.
     */
    public function setDefaultDirectoryMode(int $defaultDirectoryMode): self
    {
        if (!$this->isValidMode($defaultDirectoryMode)) {
            throw new InvalidArgumentException(self::INVALID_PERMISSION);
        }

        $this->defaultDirectoryMode = $defaultDirectoryMode;

        return $this;
    }

    /**
     * Do exclude all directories.
     */
    public function doExcludeDirectories(bool $isExcludedDirectories = false): self
    {
        $this->isExcludedDirectories = $isExcludedDirectories;

        return $this;
    }

    /**
     * Do exclude all directories.
     */
    public function doExcludeFiles(bool $isExcludedFiles = false): self
    {
        $this->isExcludedFiles = $isExcludedFiles;

        return $this;
    }

    /**
     * Is excluded files.
     */
    public function isExcludedFiles(): bool
    {
        return $this->isExcludedFiles;
    }

    /**
     * Is excluded directories.
     */
    public function isExcludedDirectories(): bool
    {
        return $this->isExcludedDirectories;
    }
}
