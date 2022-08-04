<?php
/**
 * This file is part of the php-file-permissions package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\Service\Impl;

use MathiasReker\Model\FilePermission;
use MathiasReker\Service\FilePermissionService;
use MathiasReker\Util\Iterator\Iterator;
use MathiasReker\Util\OperativeSystem;
use RecursiveIteratorIterator;

class FilePermissionServiceImpl implements FilePermissionService
{
    private Iterator $iterator;

    private FilePermission $filePermission;

    /**
     * @param string[] $directories
     */
    public function __construct(array $directories)
    {
        $this->filePermission = new FilePermission($directories);

        $this->iterator = new Iterator();
    }

    public function setExclude($setExclude): self
    {
        $this->filePermission->setExclude($setExclude);

        return $this;
    }

    /**
     * @return string[]
     */
    public function dryRun(): array
    {
        return $this->filePermission->getDisallowedModePaths();
    }

    public function fix(): void
    {
        $disallowedModePaths = $this->filePermission->getDisallowedModePaths();

        if (empty($disallowedModePaths)) {
            return;
        }

        foreach ($disallowedModePaths as $disallowedModePath) {
            clearstatcache();
            chmod(
                $disallowedModePath,
                is_dir($disallowedModePath)
                    ? $this->filePermission->getDefaultModeFolders()
                    : $this->filePermission->getDefaultModeFiles()
            );
        }
    }

    public function setDefaultModeFolder(int $defaultModeFolders): self
    {
        $this->filePermission->setDefaultModeFolder($defaultModeFolders);

        return $this;
    }

    public function setDefaultModeFile(int $defaultModeFiles): self
    {
        $this->filePermission->setDefaultModeFile($defaultModeFiles);

        return $this;
    }

    /**
     * @param int[] $allowedModeFiles
     */
    public function setAllowedModeFiles(array $allowedModeFiles): self
    {
        $this->filePermission->setAllowedModeFiles($allowedModeFiles);

        return $this;
    }

    /**
     * @param int[] $allowedModeFolders
     */
    public function setAllowedModeFolders(
        array $allowedModeFolders
    ): self {
        $this->filePermission->setAllowedModeFolders($allowedModeFolders);

        return $this;
    }

    public function scan(): self
    {
        if (OperativeSystem::isWindows()) {
            return $this;
        }

        $directories = $this->filePermission->getDirectories();

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $this->checkPerms($this->iterator->filter($directory, $this->filePermission->getExclude()));
            }
        }

        return $this;
    }

    private function checkPerms(RecursiveIteratorIterator $paths): void
    {
        $result = [];

        foreach ($paths as $path) {
            $currentMode = $path->getPerms() & 0777;

            if ($path->isDir()) {
                if (\in_array($currentMode, $this->filePermission->getAllowedModeFolders(), true)) {
                    continue;
                }
            } elseif (\in_array($currentMode, $this->filePermission->getAllowedModeFiles(), true)) {
                continue;
            }

            $result[] = $path->getRealPath();
        }

        $this->filePermission->addConcernedPaths($result);
    }

    public function setConcernedPaths(array $concernedPaths): self
    {
        $this->filePermission->addConcernedPaths($concernedPaths);

        return $this;
    }
}
