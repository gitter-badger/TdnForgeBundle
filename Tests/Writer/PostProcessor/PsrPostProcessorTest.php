<?php

namespace Tdn\ForgeBundle\Tests\Writer\PostProcessor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\ForgeBundle\TdnForgeBundle;
use Tdn\ForgeBundle\Writer\PostProcessor\PsrPostProcessor;
use Tdn\ForgeBundle\Tests\Traits\StaticData;
use \Mockery;

/**
 * Class PsrPostProcessorTest
 * @package Tdn\ForgeBundle\Tests\Writer\PostProcessor
 */
class PsrPostProcessorTest extends \PHPUnit_Framework_TestCase
{
    use StaticData;

    /**
     * @var PsrPostProcessor
     */
    protected $postProcessor;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    protected $outDir;

    protected function setUp()
    {
        $binDir = (new \SplFileInfo((new \ReflectionClass(new TdnForgeBundle()))->getFileName()))->getPath() .
            DIRECTORY_SEPARATOR . 'bin'
        ;
        $this->postProcessor = new PsrPostProcessor($binDir);
        $this->outDir        = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'forge-bundle';
        $this->filesystem   = new Filesystem();
        $this->filesystem->remove($this->outDir);
        $this->filesystem->mkdir($this->outDir);
    }

    public function testFindBin()
    {
        $binDir = (new \SplFileInfo((new \ReflectionClass(new TdnForgeBundle()))->getFileName()))->getPath() .
            DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR
        ;
        $this->postProcessor = new PsrPostProcessor($binDir);
    }

    /**
     * @expectedException \Tdn\ForgeBundle\Exception\DirectoryNotFoundException
     * @expectedExceptionMessageRegExp /Bin directory could not be found in (.*)./
     */
    public function testNoBinDir()
    {
        $binDir = (new \SplFileInfo((new \ReflectionClass(new TdnForgeBundle()))->getFileName()))->getPath() .
            DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Fixtures'
        ;
        $this->postProcessor = new PsrPostProcessor($binDir);
    }

    /**
     * @expectedException \Tdn\ForgeBundle\Exception\DirectoryNotFoundException
     * @expectedExceptionMessageRegExp /Found multiple bin directories:/
     */
    public function testTooManyBinDir()
    {
        $binDir = (new \SplFileInfo((new \ReflectionClass(new TdnForgeBundle()))->getFileName()))->getPath() .
            DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'static' .
            DIRECTORY_SEPARATOR . 'postprocessor' . DIRECTORY_SEPARATOR . 'bin-container' . DIRECTORY_SEPARATOR . 'foo'
        ;
        $this->postProcessor = new PsrPostProcessor($binDir);
    }

    public function testValid()
    {
        $this->assertTrue($this->postProcessor->isValid());
    }

    public function testSupports()
    {
        $this->assertTrue($this->postProcessor->supports($this->getFile('', true)));
    }

    public function testNoSupports()
    {
        $this->assertFalse($this->postProcessor->supports($this->getFile('', false, 'foo')));
        $this->assertNull($this->postProcessor->process($this->getFile('', false, 'foo')));
    }

    public function testProcess()
    {
        $file = new SplFileInfo($this->outDir . DIRECTORY_SEPARATOR . 'ProcessedFile.php', null, null);
        $file->openFile('w')->fwrite(self::getStaticData('postprocessor', 'UnprocessedPhpFile.phps'));
        $this->assertTrue($this->postProcessor->process($file));
        $this->assertEquals(
            self::getStaticData('postprocessor', 'ProcessedPhpFile.phps'),
            $file->getContents()
        );
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     * @expectedExceptionMessageRegExp /(.*) was not found./
     */
    public function testBadProcess()
    {
        $file = $this->getFile('', false);
        $this->postProcessor->process($file);
    }

    /**
     * @param bool $isFile
     *
     * @return SplFileInfo
     */
    private function getFile($contents, $isFile = true, $extension = 'php')
    {
        $file = Mockery::mock('\SplFileInfo');
        $file
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'isFile' => $isFile,
                'getRealPath' => $this->outDir . DIRECTORY_SEPARATOR . 'ProcessedFile.php',
                'getRealContents' => $contents,
                'getExtension'  => $extension
            ])
        ;

        return $file;
    }

    protected function tearDown()
    {
        $this->filesystem->remove($this->outDir);
        Mockery::close();
    }
}
