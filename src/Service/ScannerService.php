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

    public function setDefaultModeFile(int $defaultModeFiles);

    public function setDefaultModeFolder(int $defaultModeFolders);

    public function setAllowedModeFiles(array $allowedModeFiles);

    public function setAllowedModeFolders(array $allowedModeFolders);

    public function setExcludeNames(array $setExcludedNames);

    public function setConcernedPaths(array $concernedPaths);
}