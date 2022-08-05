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
                    ? $this->scanner->getDefaultDirectoryModes()
                    : $this->scanner->getDefaultFileModes()
            );
        }
    }

    public function setDefaultDirectoryMode(int $defaultDirectoryMode): self
    {
        $this->scanner->setDefaultDirectoryMode($defaultDirectoryMode);

        return $this;
    }

    public function setDefaultFileMode(int $defaultFileMode): self
    {
        $this->scanner->setDefaultFileMode($defaultFileMode);

        return $this;
    }

    /**
     * @param int[] $excludedFileModes
     */
    public function setExcludedFileModes(array $excludedFileModes): self
    {
        $this->scanner->setExcludedFileModes($excludedFileModes);

        return $this;
    }

    /**
     * @param int[] $excludedDirectoryModes
     */
    public function setExcludedDirectoryModes(
        array $excludedDirectoryModes
    ): self {
        $this->scanner->setExcludedDirectoryModes($excludedDirectoryModes);

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

                $this->checkMode($paths);
            }
        }

        return $this;
    }

    public function setConcernedPaths(array $concernedPaths): self
    {
        $this->scanner->addConcernedPaths($concernedPaths);

        return $this;
    }

    private function checkMode(RecursiveIteratorIterator $paths): void
    {
        $result = [];

        foreach ($paths as $path) {
            $currentMode = $path->getPerms() & 0777;

            if ($path->isDir()) {
                if (null === $this->scanner->getDefaultDirectoryModes()) {
                    continue;
                }

                if (\in_array($currentMode, $this->scanner->getExcludedDirectoryModes(), true)) {
                    continue;
                }
            } else {
                if (null === $this->scanner->getDefaultFileModes()) {
                    continue;
                }

                if (\in_array($currentMode, $this->scanner->getExcludedFileModes(), true)) {
                    continue;
                }
            }

            $result[] = $path->getRealPath();
        }

        $this->scanner->addConcernedPaths($result);
    }
}
