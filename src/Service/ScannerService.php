<?php

/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service;

use MathiasReker\PhpChmod\Model\Scanner;
use MathiasReker\PhpChmod\Util\OperatingSystem;
use Symfony\Component\Finder\Finder;

class ScannerService implements ScannerServiceInterface
{
    private readonly Scanner $scanner;

    private readonly Finder $finder;

    public function __construct()
    {
        $this->scanner = new Scanner();

        $this->finder = new Finder();
    }

    /**
     * @param list<string> $excludedNames
     */
    public function setExcludeNames(array $excludedNames): self
    {
        $this->scanner->setExcludeNames($excludedNames);

        return $this;
    }

    /**
     * @return list<string>
     */
    public function dryRun(): array
    {
        return $this->scanner->getPaths();
    }

    public function fix(): void
    {
        $paths = $this->scanner->getPaths();

        foreach ($paths as $path) {
            clearstatcache();

            chmod(
                $path,
                is_dir($path)
                    ? $this->scanner->getDefaultDirectoryMode()
                    : $this->scanner->getDefaultFileMode()
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
     * @param list<int> $excludedFileModes
     */
    public function setExcludedFileModes(array $excludedFileModes): self
    {
        $this->scanner->setExcludedFileModes($excludedFileModes);

        return $this;
    }

    /**
     * @param list<int> $excludedDirectoryModes
     */
    public function setExcludedDirectoryModes(array $excludedDirectoryModes): self
    {
        $this->scanner->setExcludedDirectoryModes($excludedDirectoryModes);

        return $this;
    }

    /**
     * @param list<string> $excludedPaths
     */
    public function setExcludedPaths(array $excludedPaths): self
    {
        $this->scanner->setExcludedPaths($excludedPaths);

        return $this;
    }

    public function doIgnoreDirectories(bool $ignoredDirectories = true): self
    {
        $this->scanner->doExcludeDirectories($ignoredDirectories);

        return $this;
    }

    public function doIgnoreFiles(bool $ignoredFiles = true): self
    {
        $this->scanner->doExcludeFiles($ignoredFiles);

        return $this;
    }

    /**
     * @param list<string> $directories
     */
    public function scan(array $directories): self
    {
        if (OperatingSystem::isWindows()) {
            return $this;
        }

        if ($this->scanner->isExcludedDirectories() && $this->scanner->isExcludedFiles()) {
            return $this;
        }

        $this->finder->ignoreUnreadableDirs();

        $this->finder->in($directories);

        if ($this->scanner->isExcludedDirectories()) {
            $this->finder->files();
        } elseif ($this->scanner->isExcludedFiles()) {
            $this->finder->directories();
        }

        if ([] !== $this->scanner->getExcludedNames()) {
            $this->finder->notName($this->scanner->getExcludedNames());
        }

        if ([] !== $this->scanner->getNames()) {
            $this->finder->name($this->scanner->getNames());
        }

        if ([] !== $this->scanner->getExcludedPaths()) {
            $this->finder->notPath($this->scanner->getExcludedPaths());
        }

        $this->finder->ignoreVCS(true);

        $this->setFilteredPaths($this->finder);

        return $this;
    }

    /**
     * Set paths matching the configuration.
     */
    private function setFilteredPaths(Finder $finder): void
    {
        $result = [];

        foreach ($finder as $path) {
            $mode = $path->getPerms() & 0o777;

            $isDir = $path->isDir();
            $excluded = $isDir
                ? $this->scanner->getExcludedDirectoryModes()
                : $this->scanner->getExcludedFileModes();

            $default = $isDir
                ? $this->scanner->getDefaultDirectoryMode()
                : $this->scanner->getDefaultFileMode();

            // Skip excluded modes and only include if mode differs from default
            if (!\in_array($mode, $excluded, true) && $mode !== $default) {
                $result[] = $path->getRealPath();
            }
        }

        $this->scanner->setPaths($result);
    }

    /**
     * @param list<string> $paths
     */
    public function setPaths(array $paths): self
    {
        $this->scanner->setPaths($paths);

        return $this;
    }

    /**
     * @param list<string> $names
     */
    public function setNames(array $names): self
    {
        $this->scanner->setNames($names);

        return $this;
    }
}
