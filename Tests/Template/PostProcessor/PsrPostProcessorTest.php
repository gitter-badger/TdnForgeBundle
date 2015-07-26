<?php

namespace Tdn\ForgeBundle\Tests\Template\PostProcessor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Tdn\ForgeBundle\TdnForgeBundle;
use Tdn\ForgeBundle\Template\PostProcessor\PsrPostProcessor;
use Tdn\ForgeBundle\Tests\Traits\StaticData;

/**
 * Class PsrPostProcessorTest
 * @package Tdn\ForgeBundle\Tests\Template\PostProcessor
 */
class PsrPostProcessorTest extends \PHPUnit_Framework_TestCase
{
    use StaticData;

    /**
     * @var PsrPostProcessor
     */
    protected $postProcessor;

    /**
     * @var string
     */
    protected $outDir;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    protected function setUp()
    {
        $binDir = (new \SplFileInfo((new \ReflectionClass(new TdnForgeBundle()))->getFileName()))->getPath() .
            DIRECTORY_SEPARATOR . 'bin'
        ;

        $this->postProcessor = new PsrPostProcessor($binDir);
        $this->outDir        = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'forge-bundle';
        $this->fileSystem    = new Filesystem();
        $this->fileSystem->remove($this->outDir);
        $this->fileSystem->mkdir($this->outDir);
    }

    public function testValid()
    {
        $this->assertTrue($this->postProcessor->isValid());
    }

    public function testSupports()
    {
        $this->assertTrue($this->postProcessor->supports($this->getFile()));
    }

    public function testProcess()
    {
        $file = $this->getFile();
        $this->assertTrue($this->postProcessor->process($file));
        $this->assertEquals(
            self::getStaticData('postprocessor', 'ProcessedPhpFile.phps'),
            $file->getContents()
        );
    }

    /**
     * @return SplFileInfo
     */
    private function getFile()
    {
        $fileName = $this->outDir . DIRECTORY_SEPARATOR . 'ProcessedFile.php';
        file_put_contents($fileName, self::getStaticData('postprocessor', 'UnprocessedPhpFile.phps'));

        return new SplFileInfo($fileName, null, null);
    }
}
