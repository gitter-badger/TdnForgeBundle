<?php

namespace Tdn\ForgeBundle\Tests\Model;

use Symfony\Component\Finder\SplFileInfo;
use Tdn\ForgeBundle\Model\File;

/**
 * Class FileTest
 * @package Tdn\ForgeBundle\Tests\Model
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    const FILE_QUEUE = 'Esto es una prueba ;)';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var File
     */
    protected $file;

    protected function setUp()
    {
        $this->fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('file') . '.php';
        $this->file = new File($this->fileName, self::FILE_QUEUE);

        touch($this->file->getRealPath());
    }

    public function testInstanceOf()
    {
        $this->assertTrue($this->file instanceof \SplFileInfo);
        $this->assertTrue($this->file instanceof SplFileInfo);
        $this->assertTrue($this->file instanceof File);
    }

    public function testDefaults()
    {
        $this->assertEmpty($this->file->getContents());
        $this->assertEquals(self::FILE_QUEUE, $this->file->getQueue());
        $this->assertEquals(File::QUEUE_DEFAULT, $this->file->getQueueType());
        $this->assertEquals($this->fileName, $this->file->getRealPath());
    }

    public function testAllowedTypes()
    {
        $this->assertContains(File::QUEUE_DEFAULT, File::getSupportedQueueTypes());
        $this->assertContains(File::QUEUE_IF_UPGRADE, File::getSupportedQueueTypes());
        $this->assertContains(File::QUEUE_ALWAYS, File::getSupportedQueueTypes());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid write type for file.
     */
    public function testInvalidType()
    {
        new File('foo', '', '');
    }

    public function testIsDirty()
    {
        $this->assertTrue($this->file->isDirty());
        $this->file->openFile('w')->fwrite(self::FILE_QUEUE);
        $this->assertFalse($this->file->isDirty());
    }

    protected function tearDown()
    {
        if ($this->file->isFile()) {
            unlink($this->file->getRealPath());
        }
    }
}
