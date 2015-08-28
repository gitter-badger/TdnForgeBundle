<?php

namespace Tdn\ForgeBundle\Writer\PostProcessor;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Tdn\ForgeBundle\Exception\DirectoryNotFoundException;
use \SplFileInfo;

abstract class AbstractPostProcessor implements PostProcessorInterface
{
    /**
     * @var \SplFileInfo
     */
    private $kernelRootDir;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var string
     */
    private $binDir;

    /**
     * @return array
     */
    abstract protected function getSupportedExtensions();

    /**
     * Default only really used when running locally (e.g. tests in bundle)

     * @param string $kernelRootDir
     */
    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = new \SplFileInfo($kernelRootDir);
        $this->finder    = new Finder();
        $this->binDir    = $this->findBinDir();
    }

    protected function getBinDir()
    {
        return $this->binDir;
    }

    /**
     * @return string
     */
    private function findBinDir()
    {
        if ($this->kernelRootDir->getBasename() == 'bin') {
            return $this->kernelRootDir->getRealPath();
        }

        $searchIn = realpath($this->kernelRootDir->getRealPath() . DIRECTORY_SEPARATOR . '..');

        $binDir = $this->finder
            ->directories()
            ->name("bin")
            ->depth("< 2")
            ->in($searchIn)
        ;

        $binDirs = iterator_to_array($binDir);

        if (count($binDirs) > 1) {
            throw new DirectoryNotFoundException(
                sprintf(
                    "Found multiple bin directories: %s%s",
                    PHP_EOL,
                    implode(PHP_EOL . '- ', $binDirs)
                )
            );
        }

        if (count($binDir) < 1) {
            throw new DirectoryNotFoundException(
                sprintf(
                    "Bin directory could not be found in %s.",
                    $searchIn
                )
            );
        }

        /** @var SplFileInfo $binDir */
        $binDir = array_pop($binDirs);

        return $binDir->getRealPath();
    }

    /**
     * @param SplFileInfo $file
     *
     * @return bool
     */
    public function supports(SplFileInfo $file)
    {
        if (in_array(mb_strtolower($file->getExtension()), $this->getSupportedExtensions())) {
            return true;
        }

        return false;
    }
}
