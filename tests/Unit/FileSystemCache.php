<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Tests\Unit;

final class FileSystemCache
{
    private string $directory;

    private int $directoryMode;

    public function __construct(string $directory, int $directoryMode)
    {
        $this->directory = $directory;

        $this->directoryMode = $directoryMode;
    }

    public function store(string $fileName, int $fileMode): void
    {
        if (!is_dir($this->directory)) {
            clearstatcache();

            mkdir($this->directory, $this->directoryMode, true);

            chmod($this->directory, $this->directoryMode); // this line is needed
        }

        $file = $this->directory . '/' . $fileName;

        if (!file_exists($file)) {
            touch($file);

            clearstatcache();

            chmod($file, $fileMode);
        }
    }
}
