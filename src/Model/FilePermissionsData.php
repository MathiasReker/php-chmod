<?php
/**
 * This file is part of the php-file-permissions package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\Model;

use MathiasReker\Exception\InvalidArgumentException;

final class FilePermissionsData
{
    private int $defaultModeFiles = 0644;

    private int $defaultModeFolders = 0755;

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
    private array $disallowedModePaths = [];

    /**
     * @var string[]
     */
    private array $directories;

    /**
     * @var string[]
     */
    private array $exclude = [];

    /**
     * @param string[] $directories
     */
    public function __construct(array $directories)
    {
        $this->directories = $directories;
    }

    public function setDefaultModeFile(int $defaultModeFiles): self
    {
        if (!$this->isValidMode($defaultModeFiles)) {
            throw new InvalidArgumentException('Invalid permission.');
        }

        $this->defaultModeFiles = $defaultModeFiles;

        return $this;
    }

    private function isValidMode(int $mode): bool
    {
        return \in_array(mb_strlen(decoct($mode)), [3, 4], true);
    }

    public function setDefaultModeFolder(int $defaultModeFolders): self
    {
        if (!$this->isValidMode($defaultModeFolders)) {
            throw new InvalidArgumentException('Invalid permission.');
        }

        $this->defaultModeFolders = $defaultModeFolders;

        return $this;
    }

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
                throw new InvalidArgumentException('Invalid permission.');
            }
        }

        $this->allowedModeFiles = $allowedModeFiles;

        return $this;
    }

    public function getAllowedModeFolders(): array
    {
        return $this->allowedModeFolders;
    }

    /**
     * @param int[] $allowedModeFolders
     */
    public function setAllowedModeFolders(array $allowedModeFolders): self
    {
        foreach ($allowedModeFolders as $allowedModeFolder) {
            if (!$this->isValidMode($allowedModeFolder)) {
                throw new InvalidArgumentException('Invalid permission.');
            }
        }

        $this->allowedModeFolders = $allowedModeFolders;

        return $this;
    }

    public function getDirectories(): array
    {
        return $this->directories;
    }

    public function getDisallowedModePaths(): array
    {
        return $this->disallowedModePaths;
    }

    public function addDisallowedModePaths($disallowedModePaths): self
    {
        $this->disallowedModePaths[] = $disallowedModePaths;

        return $this;
    }

    public function getExclude(): array
    {
        return $this->exclude;
    }

    public function setExclude(array $exclude): self
    {
        $this->exclude = $exclude;

        return $this;
    }

    public function getDefaultModeFiles(): int
    {
        return $this->defaultModeFiles;
    }

    public function getDefaultModeFolders(): int
    {
        return $this->defaultModeFolders;
    }
}
