<?php

namespace Tdn\ForgeBundle\Tests\Template\Strategy;

use Tdn\ForgeBundle\Template\PostProcessor\PostProcessorChain;
use Symfony\Component\Filesystem\Filesystem;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Template\Strategy\TwigStrategy;
use Tdn\ForgeBundle\TdnForgeBundle;
use \Mockery;

/**
 * Class TwigStrategyTest
 * @package Tdn\ForgeBundle\Tests\Template\Strategy
 */
class TwigStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $outDir;

    protected function setUp()
    {
        $this->outDir       = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-forge';
        $this->filesystem   = new Filesystem();
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    public function testRender()
    {
        $this->assertEquals('hello world', $this->getRendered());
    }

    public function testRenderFile()
    {
        $this->getTemplateStrategy()->renderFile($this->getFileMock());
        $this->assertTrue(file_exists($this->getFileMock()->getRealPath()));
        $actualFile = new File($this->getFileMock()->getRealPath());
        $this->assertEquals('hello world', $actualFile->getContents());
    }

    /**
     * @return TwigStrategy
     */
    protected function getTemplateStrategy()
    {
        $outputEngine = new TwigStrategy($this->getPostProcessorChain());
        $outputEngine->addSkeletonDir($this->getSkeletonDir());

        return $outputEngine;
    }

    /**
     * @return string
     */
    protected function getSkeletonDir()
    {
        $bundleClass    = new \ReflectionClass(new TdnForgeBundle());
        $skeletonDir = dirname($bundleClass->getFileName()) . '/Tests/Fixtures/skeleton';

        return $skeletonDir;
    }

    protected function getRendered()
    {
        return $this->getTemplateStrategy()->render('hello.txt.twig', [
            'hello_var' => 'hello world'
        ]);
    }

    /**
     * @return File
     */
    protected function getFileMock()
    {
        $file = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $file
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getContent'   => 'hello world',
                    'getContents'  => 'hello world',
                    'getFilename'  => 'hello',
                    'getPath'      => $this->getOutDir(),
                    'getExtension' => 'txt',
                    'getRealPath'  => $this->getOutDir() . DIRECTORY_SEPARATOR . 'hello.txt'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $file;
    }

    /**
     * @return string
     */
    protected function getOutDir()
    {
        return $this->outDir;
    }

    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }


    /**
     * @return PostProcessorChain
     */
    private function getPostProcessorChain()
    {
        $postProcessorChain = new PostProcessorChain();

        return $postProcessorChain;
    }
}
