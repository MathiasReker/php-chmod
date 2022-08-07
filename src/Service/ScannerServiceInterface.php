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
     * Returns array of concerned files.
     */
    public function dryRun(): array;

    /**
     * Fix concerned files.
     */
    public function fix(): void;

    /**
     * Set default file permission.
     */
    public function setDefaultFileMode(int $defaultFileMode);

    /**
     * Set default directory permission.
     */
    public function setDefaultDirectoryMode(int $defaultDirectoryMode);

    /**
     * Exclude a collection of files from the check.
     */
    public function setExcludedFileModes(array $excludedFileModes);

    /**
     * Exclude a collection of directories from the check.
     */
    public function setExcludedDirectoryModes(array $excludedDirectoryModes);

    /**
     * Exclude a collection of names from the check.
     */
    public function setExcludeNames(array $excludedNames);

    /**
     * Exclude a collection of paths from the check.
     */
    public function setExcludedPaths(array $excludedPaths);

    /**
     * Set paths manually. This is an alternative to the scanner.
     */
    public function setConcernedPaths(array $concernedPaths);

    /**
     * Set a collection of allowed names.
     */
    public function setNames(array $names);
}
