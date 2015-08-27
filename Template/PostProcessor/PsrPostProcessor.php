<?php

namespace Tdn\ForgeBundle\Template\PostProcessor;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\PhpTypes\Type\String;

/**
 * Class PsrPostProcessor
 * @package Tdn\ForgeBundle\Template\PostProcessor
 */
class PsrPostProcessor extends AbstractPostProcessor implements PostProcessorInterface
{
    /**
     * @return array
     */
    protected function getSupportedExtensions()
    {
        return [
            'php',
            'inc',
            'class',
            'php5',
            'phtml'
        ];
    }

    /**
     * @param SplFileInfo $file
     *
     * @return Process
     */
    protected function getFixerProcess(SplFileInfo $file)
    {
        return new Process(
            sprintf(
                "%s php-cs-fixer fix %s --level=psr2",
                PHP_BINARY,
                $file->getRealPath()
            ),
            $this->getBinDir()
        );
    }

    protected function getCheckerProcess(SplFileInfo $file)
    {
        return new Process(
            sprintf(
                "%s phpcs --standard=PSR2 %s",
                PHP_BINARY,
                $file->getRealPath()
            ),
            $this->getBinDir()
        );
    }

    /**
     * @throws \RuntimeException when dependencies are not met.
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * @param SplFileInfo $file
     *
     * @throw ProcessFailedException If the process ran and it failed.
     * @return bool
     */
    public function process(SplFileInfo $file)
    {
        if (!$this->supports($file)) {
            return null;
        }

        if (!$file->isFile()) {
            throw new FileNotFoundException(sprintf(
                "%s was not found.",
                $file->getRealPath()
            ));
        }

        $process = $this->getFixerProcess($file);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $output = String::create($process->getOutput());

            if (!$output->contains($file->getRealPath()) && !$output->contains('Fixed all files')) {
                throw $e;
            }
        }

        $check = $this->getCheckerProcess($file);
        $check->mustRun();

        return true;
    }
}