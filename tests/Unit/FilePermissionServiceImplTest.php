<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit;

use FilesystemIterator;
use MathiasReker\PhpChmod\Exception\InvalidArgumentException;
use MathiasReker\PhpChmod\FilePerm;
use MathiasReker\PhpChmod\Util\OperatingSystem;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @internal
 *
 * @covers \FilePermissionServiceImpl
 *
 * @small
 */
final class FilePermissionServiceImplTest extends TestCase
{
    /**
     * @var array<string, int>
     */
    private const FILE_PERMS = [
        '400.php' => 0400,
        '444.php' => 0444,
        '640.php' => 0640,
        '644.php' => 0644,
        '666.php' => 0666,
        '700.php' => 0700,
        '750.php' => 0750,
        '755.php' => 0755,
    ];

    /**
     * @var array<string, int>
     */
    private const FOLDER_PERMS = [
        'foo' => 0700,
        'bar' => 0750,
        'baz' => 0777,
    ];

    /**
     * @var string
     */
    private const ROOT = __DIR__ . '/tmp';

    protected function setUp(): void
    {
        if (OperatingSystem::isWindows()) {
            self::markTestSkipped('Tests in this class are skipped for Windows.');
        }

        foreach (self::FOLDER_PERMS as $directory => $directoryPerm) {
            foreach (self::FILE_PERMS as $file => $filePerm) {
                (new FileSystemCache(self::ROOT . '/' . $directory, $directoryPerm))
                    ->store($file, $filePerm);
            }
        }
    }

    protected function tearDown(): void
    {
        $paths = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::ROOT, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($paths as $path) {
            if ($path->isDir()) {
                rmdir($path->getRealPath());
            } else {
                unlink($path->getRealPath());
            }
        }

        rmdir(self::ROOT);
    }

    public function testFilePermissionIsNotChangedIfAllowedModeFiles(): void
    {
        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([0400])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();

        self::assertSame(0400, $this->getPerms(self::ROOT . '/foo/400.php'));
    }

    public function testFilePermissionIsChangedIfNotAllowedModeFiles(): void
    {
        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();

        self::assertSame(0644, $this->getPerms(self::ROOT . '/foo/400.php'));
    }

    public function testFolderPermissionIsNotChangedIfAllowedModeFolders(): void
    {
        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([0777])
            ->scan()
            ->fix();

        self::assertSame(0777, $this->getPerms(self::ROOT . '/baz'));
    }

    public function testFolderPermissionIsChangedIfNotAllowedModeFolders(): void
    {
        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();

        self::assertSame(0755, $this->getPerms(self::ROOT . '/baz'));
    }

    public function testFilePermissionIsChangedIfDifferentToDefault(): void
    {
        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();

        self::assertSame(0644, $this->getPerms(self::ROOT . '/bar/666.php'));
    }

    public function testDefaultFilePermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(-1)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();
    }

    public function testDefaultFilePermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(1)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();
    }

    public function testDefaultFolderPermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(-1)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();
    }

    public function testDefaultFolderPermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(1)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();
    }

    public function testAllowedFilePermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([-1])
            ->scan()
            ->fix();
    }

    public function testAllowedFilePermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([1])
            ->scan()
            ->fix();
    }

    public function testAllowedFolderPermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([-1])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();
    }

    public function testAllowedFolderPermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([1])
            ->setAllowedModeFolders([])
            ->scan()
            ->fix();
    }

    public function testDryRun(): void
    {
        $result = (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->dryRun();

        self::assertNotSame([], $result);
    }

    public function testExcludedFolders(): void
    {
        $result = (new FilePerm([self::ROOT]))
            ->setExclude(['foo'])
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->dryRun();

        self::assertNotTrue(\in_array('foo', $result, true));
    }

    public function testExcludedFiles(): void
    {
        $result = (new FilePerm([self::ROOT]))
            ->setExclude(['444.php'])
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->scan()
            ->dryRun();

        self::assertNotTrue(\in_array('444.php', $result, true));
    }

    public function testConcernedPaths(): void
    {
        $result = (new FilePerm([self::ROOT]))
            ->setDefaultModeFile(0644)
            ->setDefaultModeFolder(0755)
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->setConcernedPaths([__DIR__ . '/tmp/foo'])
            ->dryRun();

        self::assertSame(
            array_map(static fn ($x) => realpath($x), [__DIR__ . '/tmp/foo']),
            array_map(static fn ($x) => realpath($x), $result)
        );
    }

    public function testEmptyFileAndFolderPermissions(): void
    {
        $result = (new FilePerm([self::ROOT]))
            ->setAllowedModeFiles([])
            ->setAllowedModeFolders([])
            ->dryRun();

        self::assertSame(
            $result,
            []
        );
    }

    private function getPerms(string $file): int
    {
        return fileperms($file) & 0777;
    }
}
