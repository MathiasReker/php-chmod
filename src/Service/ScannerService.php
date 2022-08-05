<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service;

interface ScannerService
{
    public function dryRun();

    public function fix();

    public function setDefaultFileMode(int $defaultFileMode);

    public function setDefaultDirectoryMode(int $defaultDirectoryMode);

    public function setAllowedFileModes(array $allowedFileModes);

    public function setAllowedDirectoryModes(array $allowedDirectoryModes);

    public function setExcludeNames(array $setExcludedNames);

    public function setConcernedPaths(array $concernedPaths);
}
