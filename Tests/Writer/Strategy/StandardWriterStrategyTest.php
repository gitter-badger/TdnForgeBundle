<?php

namespace Tdn\ForgeBundle\Tests\Writer\Strategy;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Filesystem\Filesystem;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorChain;
use Tdn\ForgeBundle\Writer\Strategy\StandardWriterStrategy;
use Tdn\ForgeBundle\Writer\Strategy\WriterStrategyInterface;
use \Mockery;

/**
 * Class StandardWriterStrategyTest
 * @package Tdn\ForgeBundle\Tests\Writer\Strategy
 */
class StandardWriterStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WriterStrategyInterface
     */
    private $writerStrategy;

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
        $this->writerStrategy = new StandardWriterStrategy($this->getPostProcessorChain());
    }

    public function testWriteToExistingFile()
    {
        $this->writerStrategy->writeFile($this->getFileMock(true));
        $this->assertEquals(
            $this->getFileMock(true)->getQueue(),
            file_get_contents($this->getFileMock(true)->getRealPath())
        );
    }

    public function testWriteToNonExistentFile()
    {
        $this->filesystem->remove($this->outDir);
        $this->writerStrategy->writeFile($this->getFileMock(false));
        $this->assertEquals(
            $this->getFileMock(true)->getQueue(),
            file_get_contents($this->getFileMock(true)->getRealPath())
        );
    }

    /**
     * Expected the unexpected :O
     *
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     * @expectedExceptionMessageRegExp /Could not write file (.*). Reason: (.*)./
     */
    public function testCannotWriteFile()
    {
        $this->writerStrategy->writeFile($this->getFileMock(false, true));
    }

    /**
     * @param bool|false $isFile
     * @param bool|false $badDir
     *
     * @return File
     */
    private function getFileMock($isFile = false, $badDir = false)
    {
        $file = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $file
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getQueue'    => 'hello again',
                    'getContents' => ($isFile) ? 'hello world' : null,
                    'getPath'     => $this->outDir,
                    'isFile'      => $isFile,
                    'getRealPath' => (!$badDir) ?
                        $this->outDir . DIRECTORY_SEPARATOR . 'hello.txt' :
                        sys_get_temp_dir() . DIRECTORY_SEPARATOR . '293829' . DIRECTORY_SEPARATOR . 'hello.txt'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $file;
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
        $processor = Mockery::mock('Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorInterface');
        $processor
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'process' => null
            ])
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        $chain = Mockery::mock('Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorChain');
        $chain
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'getPostProcessorsForFile' => new ArrayCollection([$processor])
            ])
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $chain;
    }
}
