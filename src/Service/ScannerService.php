<?php
/**
 * This file is part of the php-chmod package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpChmod\Service;

use MathiasReker\PhpChmod\Model\Scanner;
use MathiasReker\PhpChmod\Util\OperatingSystem;
use Symfony\Component\Finder\Finder;

class ScannerService implements ScannerServiceInterface
{
    private Scanner $scanner;

    public function __construct()
    {
        $this->scanner = new Scanner();
    }

    public function setExcludeNames($excludedNames): self
    {
        $this->scanner->setExcludeNames($excludedNames);

        return $this;
    }

    /**
     * @return string[]
     */
    public function dryRun(): array
    {
        return $this->scanner->getConcernedPaths();
    }

    public function fix(): void
    {
        $concernedPaths = $this->scanner->getConcernedPaths();

        foreach ($concernedPaths as $concernedPath) {
            clearstatcache();

            chmod(
                $concernedPath,
                is_dir($concernedPath)
                    ? $this->scanner->getDefaultDirectoryModes()
                    : $this->scanner->getDefaultFileModes()
            );
        }
    }

    public function setDefaultDirectoryMode(int $defaultDirectoryMode): self
    {
        $this->scanner->setDefaultDirectoryMode($defaultDirectoryMode);

        return $this;
    }

    public function setDefaultFileMode(int $defaultFileMode): self
    {
        $this->scanner->setDefaultFileMode($defaultFileMode);

        return $this;
    }

    /**
     * @param int[] $excludedFileModes
     */
    public function setExcludedFileModes(
        array $excludedFileModes
    ): self {
        $this->scanner->setExcludedFileModes($excludedFileModes);

        return $this;
    }

    /**
     * @param int[] $excludedDirectoryModes
     */
    public function setExcludedDirectoryModes(
        array $excludedDirectoryModes
    ): self {
        $this->scanner->setExcludedDirectoryModes($excludedDirectoryModes);

        return $this;
    }

    public function setExcludedPaths(array $excludedPaths): self
    {
        $this->scanner->setExcludedPaths($excludedPaths);

        return $this;
    }

    public function scan(array $directories): self
    {
        if (OperatingSystem::isWindows()) {
            return $this;
        }

        if (null === $this->scanner->getDefaultDirectoryModes() && null === $this->scanner->getDefaultFileModes()) {
            return $this;
        }

        $finder = new Finder();

        $finder->ignoreUnreadableDirs();

        $finder->in($directories);

        if (null === $this->scanner->getDefaultDirectoryModes()) {
            $finder->files();
        } elseif (null === $this->scanner->getDefaultFileModes()) {
            $finder->directories();
        }

        if ([] !== $this->scanner->getExcludedNames()) {
            $finder->notName($this->scanner->getExcludedNames());
        }

        if ([] !== $this->scanner->getNames()) {
            $finder->name($this->scanner->getNames());
        }

        if ([] !== $this->scanner->getExcludedPaths()) {
            $finder->notPath($this->scanner->getExcludedPaths());
        }

        $finder->ignoreVCS(true);

        $this->checkModes($finder);

        return $this;
    }

    public function setConcernedPaths(array $concernedPaths): self
    {
        $this->scanner->addConcernedPaths($concernedPaths);

        return $this;
    }

    public function setNames(array $names): self
    {
        $this->scanner->setNames($names);

        return $this;
    }

    private function checkModes(Finder $finder): void
    {
        $result = [];

        foreach ($finder as $singleFinder) {
            $currentMode = $singleFinder->getPerms() & 0777;

            if ($singleFinder->isDir()) {
                if (\in_array($currentMode, $this->scanner->getExcludedDirectoryModes(), true)) {
                    continue;
                }
            } elseif (\in_array($currentMode, $this->scanner->getExcludedFileModes(), true)) {
                continue;
            }

            $result[] = $singleFinder->getRealPath();
        }

        $this->scanner->addConcernedPaths($result);
    }
}
