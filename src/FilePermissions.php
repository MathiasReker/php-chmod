<?php
/**
 * This file is part of the php-file-permissions package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\FilePermissions;

use InvalidArgumentException;
use RecursiveIteratorIterator;

final class FilePermissions implements FilePermsInterface
{
    private IteratorInterface $iterator;

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
    private array $exclude;

    /**
     * @param string[] $directories
     * @param string[] $exclude
     */
    public function __construct(
        array $directories,
        array $exclude = [],
        ?IteratorInterface $iterator = null
    ) {
        $this->directories = $directories;

        $this->exclude = $exclude;

        $this->iterator = $iterator ?: new Iterator();
    }

    /**
     * @return string[]
     */
    public function dryRun(): array
    {
        return $this->disallowedModePaths;
    }

    public function fix(): void
    {
        if (empty($this->disallowedModePaths)) {
            return;
        }

        foreach ($this->disallowedModePaths as $disallowedModePath) {
            chmod(
                $disallowedModePath,
                is_dir($disallowedModePath) ? $this->defaultModeFolders : $this->defaultModeFiles
            );
        }
    }

    public function setDefaultModeFile(int $defaultModeFiles): self
    {
        if (!$this->isValidMode($defaultModeFiles)) {
            throw new InvalidArgumentException('Invalid permission.');
        }

        $this->defaultModeFiles = $defaultModeFiles;

        return $this;
    }

    public function setDefaultModeFolder(int $defaultModeFolders): self
    {
        if (!$this->isValidMode($defaultModeFolders)) {
            throw new InvalidArgumentException('Invalid permission.');
        }

        $this->defaultModeFolders = $defaultModeFolders;

        return $this;
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

    /**
     * @param int[] $allowedModeFolders
     */
    public function setAllowedModeFolders(
        array $allowedModeFolders
    ): self {
        foreach ($allowedModeFolders as $allowedModeFolder) {
            if (!$this->isValidMode($allowedModeFolder)) {
                throw new InvalidArgumentException('Invalid permission.');
            }
        }

        $this->allowedModeFolders = $allowedModeFolders;

        return $this;
    }

    public function scan(): self
    {
        if ($this->isWindows()) {
            return $this;
        }

        foreach ($this->directories as $directory) {
            if (is_dir($directory)) {
                $this->checkPerms($this->iterator->filter($directory, $this->exclude));
            }
        }

        return $this;
    }

    private function checkPerms(RecursiveIteratorIterator $paths): void
    {
        foreach ($paths as $path) {
            $currentMode = $path->getPerms() & 0777;

            if ($path->isDir()) {
                if (\in_array($currentMode, $this->allowedModeFolders, true)) {
                    continue;
                }
            } else {
                if (\in_array($currentMode, $this->allowedModeFiles, true)) {
                    continue;
                }
            }

            $this->disallowedModePaths[] = $path->getRealPath();
        }
    }

    private function isWindows(): bool
    {
        return 'WIN' === mb_strtoupper(
            mb_substr(\PHP_OS, 0, 3)
        );
    }

    private function isValidMode(int $perm): bool
    {
        return \in_array(mb_strlen(decoct($perm)), [3, 4], true);
    }
}
