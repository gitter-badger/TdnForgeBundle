<?php

namespace Tdn\ForgeBundle\Tests\Generator;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Model\FormatInterface;
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

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->bundle = $this->createBundle(self::getOutDir());
        $this->filesystem   = new Filesystem();

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        $this->filesystem->remove(self::getOutDir());
        $this->filesystem->mkdir(self::getOutDir());
    }

    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param bool $forceGeneration
     *
     * @return GeneratorInterface
     */
    abstract protected function getGenerator(
        $format = FormatInterface::YAML,
        $targetDir = null,
        $overwrite = true,
        array $options = [],
        $forceGeneration = false
    );

    /**
     * @param string $format
     * @param string $targetDir
     * @param bool $overwrite
     * @param array $options
     * @param File[] $mockedFiles
     * @param array $options
     *
     * @dataProvider optionsProvider
     */
    public function testGenerate($format, $targetDir, $overwrite, array $options = [], array $mockedFiles = [])
    {
        $generator = $this->getGenerator(
            $format,
            $targetDir,
            $overwrite,
            $options,
            true
        );

        $generatedFiles = $generator->generate();

        $this->assertSameSize($mockedFiles, $generatedFiles, print_r($generatedFiles->toArray(), true));

        foreach ($generatedFiles as $generatedFile) {
            //Not get filtered contents
            $expectedContents = $mockedFiles[$generatedFile->getRealPath()]->getQueue();
            $this->assertEquals(
                $expectedContents,
                $generatedFile->getQueue(),
                'Contents don\'t match. File: ' . $generatedFile->getFilename()
            );
        }
    }

    public function testState()
    {
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            true,
            []
        );

        $this->assertEquals($this->getTemplateStrategy(), $generator->getTemplateStrategy());
        $this->assertEquals($this->getBundle(), $generator->getBundle());
        $this->assertEquals($this->getMetadata(), $generator->getMetadata());
        $this->assertEquals($this->getOutDir(), $generator->getTargetDirectory());
        $this->assertEquals(FormatInterface::YAML, $generator->getFormat());
        $this->assertEquals(self::getOutDir(), $generator->getTargetDirectory());
        $this->assertTrue($generator->shouldOverWrite());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Invalid format \w+/
     */
    public function testInvalidFormat()
    {
        $this->getGenerator(
            'Invalid',
            self::getOutDir(),
            true,
            []
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage This bundle does not support entity classes with multiple or no primary key(s).
     */
    public function testBadMetadata()
    {
        $this->metadata = $this->getMetadata();
        $this->metadata->identifier = ['now', 'invalid'];

        $this->getGenerator(
            'Invalid',
            self::getOutDir(),
            true,
            []
        );
    }

    /**
     * @expectedException \Tdn\ForgeBundle\Exception\OptionalDependencyMissingException
     * @expectedExceptionMessage Please install JMSDiExtraBundle.
     */
    public function testMissingOptionalDependency()
    {
        $generator = $this->getGenerator(
            FormatInterface::ANNOTATION,
            self::getOutDir(),
            true,
            []
        );

        $generator->generate();
    }

    public function testMessages()
    {
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            true,
            [],
            true
        );

        $this->assertEmpty($generator->getMessages()->toArray());
        $generator->generate();
        $this->assertEquals($this->getExpectedMessages(), $generator->getMessages());
    }

    public function testWillNotGenerateDupes()
    {
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            false,
            [],
            true
        );

        $firstRun = $generator->generate()->toArray();
        $secondRun = $generator->generate()->toArray();
        $this->assertSameSize($firstRun, $secondRun);
    }

    public function testWillNotGenerateExisting()
    {
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            false,
            [],
            true
        );

        /** @var File $makeExisting */
        $makeExisting = $this->getFile($generator->generate());
        if ($makeExisting) {
            mkdir(dirname($makeExisting->getRealPath()), 0755, true);
            $this->assertNotFalse(file_put_contents($makeExisting->getRealPath(), $makeExisting->getQueue()));
            $this->assertFileExists($makeExisting->getRealPath());

            $generator = $generator->reset();
            $this->assertArrayNotHasKey($makeExisting->getRealPath(), $generator->generate()->toArray());
            $this->assertContains(
                sprintf(
                    'Unable to generate queue for %s as file as it already exists. To overwrite use --overwrite.',
                    $makeExisting->getRealPath()
                ),
                $generator->getMessages()->toArray(),
                print_r($generator->getMessages()->toArray(), true)
            );
        }
    }

    public function testWillNotGenerateIfNoUpgrade()
    {
        $generator = $this->getGenerator(
            FormatInterface::YAML,
            self::getOutDir(),
            false,
            [],
            true
        );

        /** @var File $makeExisting */
        $makeExisting = $this->getFile($generator->generate(), File::QUEUE_IF_UPGRADE);

        if ($makeExisting) {
            mkdir(dirname($makeExisting->getRealPath()), 0755, true);
            //Write the file with appropriate content
            $this->assertNotFalse(file_put_contents($makeExisting->getRealPath(), $makeExisting->getQueue()));
            $this->assertFileExists($makeExisting->getRealPath());

            //Make sure it is not re-added since the content is the same.
            /** @var GeneratorInterface $generator */
            $generator = $generator->reset();
            $shouldNotContain = $generator->generate();
            $this->assertNotContains($makeExisting, $shouldNotContain);
            //Modify the file
            $this->assertNotFalse(file_put_contents($makeExisting->getRealPath(), ''));
            //Regenerate
            $generator = $generator->reset();
            //isDirty should be true.
            $shouldContain = $generator->generate();
            $this->assertArrayHasKey($makeExisting->getRealPath(), $shouldContain);
            $this->assertContains(
                sprintf(
                    '%s was upgraded.',
                    $makeExisting->getBasename('.' . $makeExisting->getExtension())
                ),
                $generator->getMessages()->toArray(),
                count($generator->getMessages())
            );
        }
    }


    /**
     * @return string
     */
    protected static function getOutDir()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tdn-forge';
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
     * Selects a non-service file.
     *
     * @param Collection $files
     * @param int $type
     *
     * @return File|null
     */
    private function getFile(Collection $files, $type = File::QUEUE_DEFAULT)
    {
        /** @var File $file */
        foreach ($files as $file) {
            if ($file->getQueueType() == $type) {
                return $file;
            }
        }

        return null;
    }
}
