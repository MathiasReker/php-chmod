<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service;

interface ScannerServiceInterface
{
    /**
     * Returns a collection of concerned directories/files.
     *
     * @return string[]
     */
    public function dryRun(): array;

    /**
     * Fix concerned directories/files.
     */
    public function fix(): void;

    /**
     * Build the list of concerned directories/files.
     *
     * @param string[] $directories
     */
    public function scan(array $directories): self;

    /**
     * Set default file permission.
     */
    public function setDefaultFileMode(int $defaultFileMode): self;

    /**
     * Set default directory permission.
     */
    public function setDefaultDirectoryMode(int $defaultDirectoryMode): self;

    /**
     * Set excluded permissions for files.
     *
     * @param int[] $excludedFileModes
     */
    public function setExcludedFileModes(array $excludedFileModes): self;

    /**
     * Set excluded permissions for directories.
     *
     * @param int[] $excludedDirectoryModes
     */
    public function setExcludedDirectoryModes(array $excludedDirectoryModes): self;

    /**
     * Set names that directories/files must match.
     *
     * @param string[] $excludedNames
     */
    public function setExcludeNames(array $excludedNames): self;

    /**
     * Set names the directories/files must match.
     *
     * @param string[] $names
     */
    public function setNames(array $names): self;

    /**
     * Set excluded paths.
     *
     * @param string[] $excludedPaths
     */
    public function setExcludedPaths(array $excludedPaths): self;

    /**
     * Set paths manually. This is an alternative to the scanner.
     *
     * @param string[] $paths
     */
    public function setPaths(array $paths): self;

    /**
     * Ignore all directories.
     */
    public function doIgnoreDirectories(bool $ignoredDirectories): self;

    /**
     * Ignore all files.
     */
    public function doIgnoreFiles(bool $ignoredFiles): self;
}
