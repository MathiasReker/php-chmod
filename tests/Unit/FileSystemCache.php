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

    private int $folderPerm;

    public function __construct(string $directory, int $perm)
    {
        $this->directory = $directory;

        $this->folderPerm = $perm;
    }

    public function store(string $fileName, int $filePerm): void
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, $this->folderPerm, true);
            chmod($this->directory, $this->folderPerm); // this line is needed
            clearstatcache();
        }

        $file = $this->directory . '/' . $fileName;
        if (!file_exists($file)) {
            touch($file);
        }

        chmod($file, $filePerm);
        clearstatcache();
    }
}
