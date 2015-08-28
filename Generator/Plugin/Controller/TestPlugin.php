<?php

namespace Tdn\ForgeBundle\Generator\Plugin\Controller;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tdn\ForgeBundle\ClassLoader\ClassMapGenerator;
use Tdn\ForgeBundle\Exception\PluginInstallException;
use Tdn\ForgeBundle\Generator\Plugin\AbstractPlugin;
use Tdn\ForgeBundle\Generator\Plugin\PluginInterface;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Template\Strategy\TemplateStrategyInterface;
use Tdn\ForgeBundle\Traits\Bundled;
use Tdn\ForgeBundle\Traits\DoctrineMetadata;
use Tdn\ForgeBundle\Traits\FileDependencies;
use Tdn\ForgeBundle\Traits\Files;
use Tdn\ForgeBundle\Traits\OptionalDependency;
use Tdn\ForgeBundle\Traits\OverWritable;
use Tdn\ForgeBundle\Traits\TargetedOutput;
use Tdn\PhpTypes\Type\String;

/**
 * Class TestPlugin
 * @package Tdn\ForgeBundle\Generator\Plugin\Controller
 */
class TestPlugin extends AbstractPlugin implements PluginInterface
{
    use OverWritable;
    use TargetedOutput;
    use FileDependencies;
    use Bundled;
    use DoctrineMetadata;
    use OptionalDependency;
    use Files;

    const FIXTURE_ALICE = 'alice';
    const FIXTURE_DOCTRINE = 'doctrine';

    /**
     * @var string
     */
    private $fixturesPath;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @param TemplateStrategyInterface $templateStrategy
     * @param BundleInterface $bundle
     * @param ClassMetadata $metadata
     * @param string $targetDirectory
     * @param string $fixturesPath
     * @param bool $overWrite
     */
    public function __construct(
        TemplateStrategyInterface $templateStrategy,
        BundleInterface $bundle,
        ClassMetadata $metadata,
        $targetDirectory,
        $fixturesPath,
        $overWrite
    ) {
        $this->templateStrategy = $templateStrategy;
        $this->bundle           = $bundle;
        $this->metadata         = $metadata;
        $this->targetDirectory  = $targetDirectory;
        $this->fixturesPath     = $fixturesPath;
        $this->overWrite        = $overWrite;
        $this->finder           = new Finder();

        $abstractControllerTest = $this->getAbstractControllerTest();
        $controllerTest         = $this->getControllerTest();

        if (!$abstractControllerTest->isFile() || $this->shouldOverWrite()) {
            $this->addFile($abstractControllerTest);
        }

        if (!$controllerTest->isFile() || $this->shouldOverWrite()) {
            $this->addFile($controllerTest);
        }
    }

    /**
     * @throws PluginInstallException
     *
     * @return bool
     */
    public function isInstallable()
    {
        //No extra dependencies.
        return true;
    }

    /**
     * @return bool
     */
    protected function getFixturesPath()
    {
        return $this->fixturesPath;
    }

    /**
     * @return File
     */
    protected function getControllerTest()
    {
        $controllerTest = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Controller' .
                DIRECTORY_SEPARATOR . '%sControllerTest.php',
                ($this->targetDirectory) ?: realpath($this->getBundle()->getPath()),
                $this->getEntity()
            ),
            $this->getControllerTestContent(),
            File::QUEUE_ALWAYS
        );

        return $controllerTest;
    }

    /**
     * Generates the functional test class only.
     *
     * @return string The file contents
     */
    protected function getControllerTestContent()
    {
        return $this->templateStrategy->render(
            'controller/controller-test.php.twig',
            [
                'entity'                 => $this->getEntity(),
                'namespace'              => $this->getBundle()->getNamespace()
            ]
        );
    }

    /**
     * @return File
     */
    protected function getAbstractControllerTest()
    {
        $abstractControllerTest = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR . 'Controller' .
                DIRECTORY_SEPARATOR . 'AbstractControllerTest.php',
                ($this->targetDirectory) ?: realpath($this->getBundle()->getPath())
            ),
            $this->getAbstractControllerTestContent()
        );

        return $abstractControllerTest;
    }

    /**
     * @return string
     */
    protected function getAbstractControllerTestContent()
    {
        return $this->templateStrategy->render(
            'controller/abstract-controller-test.php.twig',
            [
                'entity'    => $this->getEntity(),
                'namespace' => $this->getBundle()->getNamespace(),
                'fixtures'  => $this->getRelevantFixtures($this->getFixturesPath())
            ]
        );
    }

    /**
     * @param string $directory Where to find the fixtures.
     *
     * @return array
     */
    private function getRelevantFixtures($directory)
    {
        switch ($this->getFixtureTypeInDirectory($directory)) {
            case self::FIXTURE_ALICE:
                return $this->parseAliceFixtures($directory);
            case self::FIXTURE_DOCTRINE:
                return $this->parseDoctrineFixtures($directory);
            default:
                throw new PluginInstallException(
                    'No supported fixture type was detected. ' .
                    'At least one php file, or yaml file should be in the directory.'
                );
        }
    }

    private function parseDoctrineFixtures($directory)
    {
        $potentialFixtures = $this->getReflectedDoctrineFixtures($this->getFullyQualifiedClassesInDir($directory));
        $actualFixtures  = [];

        return $actualFixtures;
    }

    /**
     * @param $directory
     *
     * @return string
     */
    private function getFixtureTypeInDirectory($directory)
    {
        return '';
    }

    /**
     * Returns array containing the fully qualified representation of classes in a given directory.
     *
     * @param $directory
     * @return array
     */
    private function getFullyQualifiedClassesInDir($directory)
    {
        return array_keys(ClassMapGenerator::createMap($directory));
    }

    /**
     * @param string $fullyQualified
     *
     * @return string
     */
    private function getNamespaceFromFullyQualifiedClass($fullyQualified)
    {
        $fullyQualified = String::create($fullyQualified);
        return (string) $fullyQualified->substr(0, $fullyQualified->strrpos('\\'));
    }

    private function getRequiredFixtureMap()
    {
        return array_merge(
            [$this->getEntity()],
            array_map(
                function ($v) {
                    return $v['targetEntity'];
                },
                $this->getMetadata()->associationMappings
            )
        );
    }

    /**
     * @param array $fqdnArray
     *
     * @return \ReflectionClass[]
     */
    private function getReflectedDoctrineFixtures($fqdnArray)
    {
    }
}
