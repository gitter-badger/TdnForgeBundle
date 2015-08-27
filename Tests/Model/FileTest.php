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
    const FILE_CONTENTS = 'Esto es una prueba ;)';
    /**
     * @var File
     */
    protected $file;

    protected function setUp()
    {
        $this->file = new File(
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('file') . '.php',
            self::FILE_CONTENTS
        );

        touch($this->file->getRealPath());
    }

    public function testInstanceOf()
    {
        $this->assertTrue($this->file instanceof \SplFileInfo);
        $this->assertTrue($this->file instanceof SplFileInfo);
        $this->assertTrue($this->file instanceof File);
    }

    public function testContents()
    {
        $this->file->openFile('w')->fwrite('test');
        $this->assertEquals('test', $this->file->getContents());
        $this->assertTrue($this->file->isFile());
    }

    public function testContent()
    {
        $this->assertEquals(self::FILE_CONTENTS, $this->file->getContent());
    }

    public function testAuxFile()
    {
        $this->assertFalse($this->file->isAuxFile());
        $this->file->setAuxFile(true);
        $this->assertTrue($this->file->isAuxFile());
    }

    public function testServiceFile()
    {
        $this->assertFalse($this->file->isServiceFile());
        $this->file->setServiceFile(true);
        $this->assertTrue($this->file->isServiceFile());
    }

    public function testIsDirty()
    {
        $this->assertTrue($this->file->isDirty());
        $this->file->openFile('w')->fwrite(self::FILE_CONTENTS);
        $this->assertFalse($this->file->isDirty());
    }


    protected function tearDown()
    {
        if ($this->file->isFile()) {
            unlink($this->file->getRealPath());
        }

        $this->file = null;
    }
}
