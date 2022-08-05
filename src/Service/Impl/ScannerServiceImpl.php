<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service\Impl;

use MathiasReker\PhpChmod\Model\Scanner;
use MathiasReker\PhpChmod\Service\ScannerService;
use MathiasReker\PhpChmod\Util\OperatingSystem;
use RecursiveIteratorIterator;

class ScannerServiceImpl implements ScannerService
{
    private Scanner $scanner;

    public function __construct()
    {
        $this->scanner = new Scanner();
    }

    public function setExcludeNames($setExcludedNames): self
    {
        $this->scanner->setExcludeNames($setExcludedNames);

        return $this;
    }

    /**
     * @return string[]
     */
    public function dryRun(): array
    {
        return $this->scanner->getConcernedPaths();
    }

    public function fix(): void
    {
        $concernedPaths = $this->scanner->getConcernedPaths();

        if (empty($concernedPaths)) {
            return;
        }

        foreach ($concernedPaths as $concernedPath) {
            clearstatcache();

            chmod(
                $concernedPath,
                is_dir($concernedPath)
                    ? $this->scanner->getDefaultModeFolders()
                    : $this->scanner->getDefaultModeFiles()
            );
        }
    }

    public function setDefaultModeFolder(int $defaultModeFolders): self
    {
        $this->scanner->setDefaultModeFolder($defaultModeFolders);

        return $this;
    }

    public function setDefaultModeFile(int $defaultModeFiles): self
    {
        $this->scanner->setDefaultModeFile($defaultModeFiles);

        return $this;
    }

    /**
     * @param int[] $allowedModeFiles
     */
    public function setAllowedModeFiles(array $allowedModeFiles): self
    {
        $this->scanner->setAllowedModeFiles($allowedModeFiles);

        return $this;
    }

    /**
     * @param int[] $allowedModeFolders
     */
    public function setAllowedModeFolders(
        array $allowedModeFolders
    ): self {
        $this->scanner->setAllowedModeFolders($allowedModeFolders);

        return $this;
    }

    public function scan(array $directories): self
    {
        if (OperatingSystem::isWindows()) {
            return $this;
        }

        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                $paths = (new IteratorServiceImpl())
                    ->setDirectory($directory)
                    ->setExcludedNames($this->scanner->getExcludedNames())
                    ->getPaths();

                $this->checkPerms($paths);
            }
        }

        return $this;
    }

    public function setConcernedPaths(array $concernedPaths): self
    {
        $this->scanner->addConcernedPaths($concernedPaths);

        return $this;
    }

    private function checkPerms(RecursiveIteratorIterator $paths): void
    {
        $result = [];

        foreach ($paths as $path) {
            $currentMode = $path->getPerms() & 0777;

            if ($path->isDir()) {
                if (null === $this->scanner->getDefaultModeFolders()) {
                    continue;
                }

                if (\in_array($currentMode, $this->scanner->getAllowedModeFolders(), true)) {
                    continue;
                }
            } else {
                if (null === $this->scanner->getDefaultModeFiles()) {
                    continue;
                }

                if (\in_array($currentMode, $this->scanner->getAllowedModeFiles(), true)) {
                    continue;
                }
            }

            $result[] = $path->getRealPath();
        }

        $this->scanner->addConcernedPaths($result);
    }
}
