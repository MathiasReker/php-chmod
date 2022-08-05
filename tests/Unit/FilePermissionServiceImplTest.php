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
use MathiasReker\PhpChmod\Scanner;
use MathiasReker\PhpChmod\Util\OperatingSystem;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @internal
 *
 * @covers \ScannerServiceImpl
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
        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([0400])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0400, $this->getPerms(self::ROOT . '/foo/400.php'));
    }

    public function testFilePermissionIsChangedIfNotAllowedModeFiles(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0644, $this->getPerms(self::ROOT . '/foo/400.php'));
    }

    public function testFolderPermissionIsNotChangedIfAllowedModeFolders(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([0777])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0777, $this->getPerms(self::ROOT . '/baz'));
    }

    public function testFolderPermissionIsChangedIfNotAllowedModeFolders(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0755, $this->getPerms(self::ROOT . '/baz'));
    }

    public function testFilePermissionIsChangedIfDifferentToDefault(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0644, $this->getPerms(self::ROOT . '/bar/666.php'));
    }

    public function testDefaultFilePermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(-1)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDefaultFilePermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(1)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDefaultFolderPermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(-1)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDefaultFolderPermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(1)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testAllowedFilePermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([-1])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testAllowedFilePermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([1])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testAllowedFolderPermissionIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([-1])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testAllowedFolderPermissionIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([1])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDryRun(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertNotSame([], $result);
    }

    public function testExcludedFolders(): void
    {
        $result = (new Scanner())
            ->setExcludeNames(['foo'])
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertNotTrue(\in_array('foo', $result, true));
    }

    public function testExcludedFiles(): void
    {
        $result = (new Scanner())
            ->setExcludeNames(['444.php'])
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertNotTrue(\in_array('444.php', $result, true));
    }

    public function testConcernedPaths(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0644)
            ->setDefaultDirectoryMode(0755)
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->setConcernedPaths([__DIR__ . '/tmp/foo'])
            ->dryRun();

        self::assertSame(
            array_map(static fn ($x) => realpath($x), [__DIR__ . '/tmp/foo']),
            array_map(static fn ($x) => realpath($x), $result)
        );
    }

    public function testEmptyFileAndFolderPermissions(): void
    {
        $result = (new Scanner())
            ->setAllowedFileModes([])
            ->setAllowedDirectoryModes([])
            ->scan([self::ROOT])
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
