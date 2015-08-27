<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Generator\Plugin\PluginInterface;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Tests\Traits\BundleMock;
use Tdn\ForgeBundle\Tests\Traits\MetadataMock;
use Tdn\ForgeBundle\Tests\Traits\StaticData;
use Tdn\ForgeBundle\Tests\Traits\TwigStrategyMock;
use \Mockery;

/**
 * Class AbstractGeneratorTest
 * @package Tdn\ForgeBundle\Tests\Generator
 */
abstract class AbstractGeneratorTest extends \PHPUnit_Framework_TestCase implements GeneratorTestInterface
{
    use MetadataMock;
    use BundleMock;
    use TwigStrategyMock;
    use StaticData;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var PluginInterface
     */
    private $plugin;

    /**
     * @param array $options
     *
     * @return GeneratorInterface
     */
    abstract protected function getGenerator(array $options = []);

    /**
     * @return ArrayCollection|File[]
     */
    abstract protected function getFileDependencies();

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->bundle = $this->createBundle(self::getOutDir());
        $this->plugin = $this->createPlugin();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @param string $format
     * @param bool $overWrite
     * @param File[] $mockedFiles
     * @param array $options
     *
     * @dataProvider optionsProvider
     */
    public function testGenerate(
        $format,
        $overWrite,
        array $mockedFiles,
        array $options = []
    ) {
        $generator = $this->getGenerator($options);
        $generator->setFormat($format);
        $generator->setOverWrite($overWrite);
        $generator->setTargetDirectory(self::getOutDir()); //Ensure test directory
        $generator->configure(); //Set up generated file objects with updated format and target directory.
        $generator->setFileDependencies(new ArrayCollection()); //ignore dependencies for the test.
        $this->assertTrue($generator->isValid()); //And now should be valid...
        $generatedFiles = $generator->generate();

        $this->assertSameSize($mockedFiles, $generatedFiles, print_r($generatedFiles->toArray(), true));

        foreach ($generatedFiles as $generatedFile) {
            //Not get filtered contents
            $expectedContents = $mockedFiles[$generatedFile->getRealPath()]->getContent();
            $this->assertEquals(
                $expectedContents,
                $generatedFile->getContents(),
                'Contents don\'t match. File: ' . $generatedFile->getFilename()
            );
        }
    }

    protected function setUp()
    {
        $this->filesystem   = new Filesystem();
        $this->filesystem->remove(self::getOutDir());
        $this->filesystem->mkdir(self::getOutDir());
    }

    public function testOutputEngine()
    {
        $this->assertEquals($this->getTemplateStrategy(), $this->getGenerator()->getTemplateStrategy());
    }

    public function testBundle()
    {
        $this->assertEquals($this->getBundle(), $this->getGenerator()->getBundle());
    }

    public function testEntity()
    {
        $this->assertEquals('Foo', $this->getGenerator()->getEntity());
    }

    public function testEntityNamespace()
    {
        $this->assertEquals('', $this->getGenerator()->getEntityNamespace());
    }

    public function testMetadata()
    {
        $this->assertEquals($this->getMetadata(), $this->getGenerator()->getMetadata());
    }

    public function testFileDependencies()
    {
        $this->assertEquals($this->getFileDependencies(), $this->getGenerator()->configure()->getFileDependencies());
    }

    public function testTargetDirectory()
    {
        $this->assertEquals($this->getOutDir(), $this->getGenerator()->getTargetDirectory());
    }

    public function testMessages()
    {
        $generator = $this->getGenerator()->configure();
        $this->assertEquals($this->getExpectedMessages(), $generator->getMessages());
    }

    public function testPlugins()
    {
        $plugin      = $this->getPlugin();
        $generator = $this->getGenerator();
        $this->assertEmpty($generator->getPlugins());
        $generator->addPlugin($plugin);
        $this->assertContains($plugin, $generator->getPlugins());
    }

    public function testOverwrite()
    {
        $this->assertEquals(false, $this->getGenerator()->shouldOverWrite());
        $generator = $this->getGenerator();
        $generator->setOverWrite(true);
        $this->assertEquals(true, $generator->shouldOverWrite());
    }

    /**
     * @return string
     */
    protected static function getOutDir()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-forge';
    }

    /**
     * @return PluginInterface
     */
    protected function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return ArrayCollection
     */
    protected function getExpectedMessages()
    {
        return new ArrayCollection();
    }

    protected function tearDown()
    {
        $this->filesystem->remove(self::getOutDir());
        Mockery::close();
    }

    /**
     * @return PluginInterface
     */
    private function createPlugin()
    {
        $plugin = Mockery::mock('\Tdn\ForgeBundle\Generator\Plugin\PluginInterface');
        $plugin->shouldDeferMissing();

        return $plugin;
    }
}
