<?php

/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Tests\Unit;

use MathiasReker\PhpChmod\Exception\InvalidArgumentException;
use MathiasReker\PhpChmod\Scanner;
use MathiasReker\PhpChmod\Util\OperatingSystem;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\ScannerService::class)]
#[\PHPUnit\Framework\Attributes\Small]
final class ScannerTest extends TestCase
{
    /**
     * @var array<string, int>
     */
    private const FILE_MODES = [
        '400.php' => 0o400,
        '444.php' => 0o444,
        '640.php' => 0o640,
        '644.php' => 0o644,
        '666.php' => 0o666,
        '700.php' => 0o700,
        '750.php' => 0o750,
        '755.php' => 0o755,
        'test.sh' => 0o777,
    ];

    /**
     * @var array<string, int>
     */
    private const DIRECTORY_MODES = [
        'foo' => 0o700,
        'bar' => 0o750,
        'baz' => 0o777,
    ];

    /**
     * @var string
     */
    private const ROOT = __DIR__ . '/tmp';

    public function testFileModeIsNotChangedIfExcludedFileModes(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([0o400])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0o400, $this->getMode(self::ROOT . '/foo/400.php'));
    }

    private function getMode(string $file): int
    {
        return fileperms($file) & 0o777;
    }

    public function testFileModeIsChangedIfNotExcludedFileModes(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0o644, $this->getMode(self::ROOT . '/foo/400.php'));
    }

    public function testDirectoryModeIsNotChangedIfExcludedDirectoryModes(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([0o777])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0o777, $this->getMode(self::ROOT . '/baz'));
    }

    public function testDirectoryModeIsChangedIfNotExcludedDirectoryModes(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0o755, $this->getMode(self::ROOT . '/baz'));
    }

    public function testFileModeIsChangedIfDifferentToDefault(): void
    {
        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();

        self::assertSame(0o644, $this->getMode(self::ROOT . '/bar/666.php'));
    }

    public function testDefaultFileModeIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(-1)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDefaultFileModeIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(1)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDefaultDirectoryModeIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(-1)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDefaultDirectoryModeIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(1)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testExcludedFileModeIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([-1])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testExcludedFileModeIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([1])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testExcludedDirectoryModeIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([-1])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testExcludedDirectoryModeIsNotValid2(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([1])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->fix();
    }

    public function testDryRun(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertNotSame([], $result);
    }

    public function testExcludedDirectories(): void
    {
        $result = (new Scanner())
            ->setExcludeNames(['foo'])
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertNotContains('foo', $result);
    }

    public function testExcludedFiles(): void
    {
        $result = (new Scanner())
            ->setExcludeNames(['444.php'])
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertNotContains('444.php', $result);
    }

    public function testConcernedPaths(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->setPaths([__DIR__ . '/tmp/foo'])
            ->dryRun();

        self::assertSame(
            array_map(realpath(...), [__DIR__ . '/tmp/foo']),
            array_map(realpath(...), $result)
        );
    }

    public function testExcludedPhpFiles(): void
    {
        $result = (new Scanner())
            ->setExcludeNames(['*.php'])
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && !\in_array(realpath(__DIR__ . '/tmp/foo/444.php'), $result, true));
    }

    public function testExcludedShellFilesButIncludedEverythingElse(): void
    {
        $result = (new Scanner())
            ->setExcludeNames(['*.sh'])
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && !\in_array(realpath(__DIR__ . '/tmp/foo/test.sh'), $result, true));
    }

    public function testIncludeOnlyPhpFiles(): void
    {
        $result = (new Scanner())
            ->setNames(['*.php'])
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedDirectoryModes([])
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && !\in_array(realpath(__DIR__ . '/tmp/foo/test.sh'), $result, true));
    }

    public function testExcludedPaths(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0o644)
            ->setDefaultDirectoryMode(0o755)
            ->setExcludedFileModes([])
            ->setExcludedPaths(['baz'])
            ->scan([self::ROOT])
            ->dryRun();

        $result = array_map(realpath(...), $result);

        self::assertTrue([] !== $result
            && !\in_array(realpath(__DIR__ . '/tmp/baz/755.php'), $result, true));
    }

    public function testOnlyFindFiles(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0o644)
            ->doIgnoreDirectories()
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && !\in_array(realpath(__DIR__ . '/tmp/bar'), $result, true));
    }

    public function testNotOnlyFindFiles(): void
    {
        $result = (new Scanner())
            ->setDefaultFileMode(0o644)
            ->doIgnoreDirectories(false)
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && \in_array(realpath(__DIR__ . '/tmp/bar'), $result, true));
    }

    public function testOnlyFindDirectories(): void
    {
        $result = (new Scanner())
            ->setDefaultDirectoryMode(0o755)
            ->doIgnoreFiles()
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && !\in_array(realpath(__DIR__ . '/tmp/bar/755.php'), $result, true));
    }

    public function testNotOnlyFindDirectories(): void
    {
        $result = (new Scanner())
            ->setDefaultDirectoryMode(0o755)
            ->doIgnoreFiles(false)
            ->scan([self::ROOT])
            ->dryRun();

        self::assertTrue([] !== $result
            && \in_array(realpath(__DIR__ . '/tmp/bar/755.php'), $result, true));
    }

    public function testEmptyFileAndDirectoryModes(): void
    {
        $result = (new Scanner())
            ->doIgnoreDirectories()
            ->doIgnoreFiles()
            ->scan([self::ROOT])
            ->dryRun();

        self::assertSame($result, []);
    }

    protected function setUp(): void
    {
        if (OperatingSystem::isWindows()) {
            self::markTestSkipped('Tests in this class are skipped for Windows.');
        }

        foreach (self::DIRECTORY_MODES as $directory => $directoryMode) {
            foreach (self::FILE_MODES as $file => $fileMode) {
                (new FileSystemCache(self::ROOT . '/' . $directory, $directoryMode))
                    ->store($file, $fileMode);
            }
        }
    }

    protected function tearDown(): void
    {
        $paths = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::ROOT, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($paths as $path) {
            if ($path->isDir()) {
                rmdir($path->getRealPath());
            } else {
                unlink($path->getRealPath());
            }
        }

        rmdir(self::ROOT);
    }
}
