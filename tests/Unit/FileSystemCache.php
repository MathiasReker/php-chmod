<?php
/**
 * This file is part of the php-file-permissions package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit;

final class FileSystemCache
{
    private string $directory;

    private int $directoryPermission;

    public function __construct(string $directory, int $directoryPermission)
    {
        $this->directory = $directory;

        $this->directoryPermission = $directoryPermission;
    }

    public function store(string $fileName, int $filePermission): void
    {
        if (!is_dir($this->directory)) {
            clearstatcache();
            mkdir($this->directory, $this->directoryPermission, true);
            chmod($this->directory, $this->directoryPermission); // this line is needed
        }

        $file = $this->directory . '/' . $fileName;
        if (!file_exists($file)) {
            touch($file);
            clearstatcache();
            chmod($file, $filePermission);
        }
    }
}
