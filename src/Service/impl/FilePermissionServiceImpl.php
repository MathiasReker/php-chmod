<?php
/**
 * This file is part of the php-file-permissions package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\Service\impl;

use MathiasReker\Model;
use MathiasReker\Model\FilePermissions;
use MathiasReker\Service\FilePermissionService;
use MathiasReker\Util\Iterator\Iterator;
use MathiasReker\Util\OperativeSystem;
use RecursiveIteratorIterator;

class FilePermissionServiceImpl implements FilePermissionService
{
    private Iterator $iterator;

    private Model\FilePermissions $filePermissions;

    /**
     * @param string[] $directories
     */
    public function __construct(array $directories)
    {
        $this->filePermissions = new FilePermissions($directories);

        $this->iterator = new Iterator();
    }

    public function setExclude($setExclude): self
    {
        $this->filePermissions->setExclude($setExclude);

        return $this;
    }

    /**
     * @return string[]
     */
    public function dryRun(): array
    {
        return $this->filePermissions->getDisallowedModePaths();
    }

    public function fix(): void
    {
        $disallowedModePaths = $this->filePermissions->getDisallowedModePaths();

        if (empty($disallowedModePaths)) {
            return;
        }

        foreach ($disallowedModePaths as $disallowedModePath) {
            clearstatcache();
            chmod(
                $disallowedModePath,
                is_dir($disallowedModePath)
                    ? $this->filePermissions->getDefaultModeFolders()
                    : $this->filePermissions->getDefaultModeFiles()
            );
        }
    }

    public function setDefaultModeFolder(int $defaultModeFolders): self
    {
        $this->filePermissions->setDefaultModeFolder($defaultModeFolders);

        return $this;
    }

    public function setDefaultModeFile(int $defaultModeFiles): self
    {
        $this->filePermissions->setDefaultModeFile($defaultModeFiles);

        return $this;
    }

    /**
     * @param int[] $allowedModeFiles
     */
    public function setAllowedModeFiles(array $allowedModeFiles): self
    {
        $this->filePermissions->setAllowedModeFiles($allowedModeFiles);

        return $this;
    }

    /**
     * @param int[] $allowedModeFolders
     */
    public function setAllowedModeFolders(array $allowedModeFolders): self
    {
        $this->filePermissions->setAllowedModeFolders($allowedModeFolders);

        return $this;
    }

    public function scan(): self
    {
        if (OperativeSystem::isWindows()) {
            return $this;
        }

        $directories = $this->filePermissions->getDirectories();

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $this->checkPerms($this->iterator->filter($directory, $this->filePermissions->getExclude()));
            }
        }

        return $this;
    }

    private function checkPerms(RecursiveIteratorIterator $paths): void
    {
        foreach ($paths as $path) {
            $currentMode = $path->getPerms() & 0777;

            if ($path->isDir()) {
                if (\in_array($currentMode, $this->filePermissions->getAllowedModeFolders(), true)) {
                    continue;
                }
            } else {
                if (\in_array($currentMode, $this->filePermissions->getAllowedModeFiles(), true)) {
                    continue;
                }
            }

            $this->filePermissions->addDisallowedModePaths($path->getRealPath());
        }
    }
}
