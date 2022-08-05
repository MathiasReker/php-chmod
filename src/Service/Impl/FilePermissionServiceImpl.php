<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service\Impl;

use MathiasReker\PhpChmod\Model\FilePermission;
use MathiasReker\PhpChmod\Service\FilePermissionService;
use MathiasReker\PhpChmod\Util\Iterator\Iterator;
use MathiasReker\PhpChmod\Util\OperativeSystem;
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
        return $this->filePermission->getConcernedPaths();
    }

    public function fix(): void
    {
        $concernedPaths = $this->filePermission->getConcernedPaths();

        if (empty($concernedPaths)) {
            return;
        }

        foreach ($concernedPaths as $concernedPath) {
            clearstatcache();

            chmod(
                $concernedPath,
                is_dir($concernedPath)
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

    public function setConcernedPaths(array $concernedPaths): self
    {
        $this->filePermission->addConcernedPaths($concernedPaths);

        return $this;
    }

    private function checkPerms(RecursiveIteratorIterator $paths): void
    {
        $result = [];

        foreach ($paths as $path) {
            $currentMode = $path->getPerms() & 0777;

            if ($path->isDir()) {
                if (null === $this->filePermission->getDefaultModeFolders()) {
                    continue;
                }

                if (\in_array($currentMode, $this->filePermission->getAllowedModeFolders(), true)) {
                    continue;
                }
            } else {
                if (null === $this->filePermission->getDefaultModeFiles()) {
                    continue;
                }

                if (\in_array($currentMode, $this->filePermission->getAllowedModeFiles(), true)) {
                    continue;
                }
            }

            $result[] = $path->getRealPath();
        }

        $this->filePermission->addConcernedPaths($result);
    }
}
