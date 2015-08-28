<?php

namespace Tdn\ForgeBundle\Services\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Finder\Finder;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Tdn\PhpTypes\Type\String;

/**
 * Class EntityHelper
 * @package Tdn\ForgeBundle\Services\Doctrine
 */
class EntityHelper
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->managerRegistry = $registry;
    }

    /**
     * @return ManagerRegistry
     */
    public function getManagerRegistry()
    {
        return $this->managerRegistry;
    }

    /**
     * @param string $directory
     * @param string $bundleName
     * @param array|string $excludes
     *
     * @return ArrayCollection
     */
    public function getClassesInDirectory($directory, $bundleName, array $excludes = [])
    {
        $entities = new ArrayCollection();

        $finder = new Finder();
        $finder
            ->files()
            ->in($directory)
            ->name('*.php')
            ->notName('/interface/i')
            ->notName('/manager/i')
            ->notName('/repository/i')
        ;

        foreach ($excludes as $exclude) {
            $finder->notName(
                sprintf(
                    '/%s/i',
                    $exclude
                )
            );
        }

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $entities->add($this->getEntityShortcutFromPath($file->getRealPath(), $bundleName));
        }

        return $entities;
    }

    /**
     * @param BundleInterface $bundle
     * @param string $entity
     *
     * @return ClassMetadata
     */
    public function getMetadata(BundleInterface $bundle, $entity)
    {
        $entity = $this->getManagerRegistry()->getAliasNamespace($bundle->getName()) . '\\' . $entity;

        try {
            return $this->getManagerRegistry()->getManagerForClass($entity)
                ->getMetadataFactory()
                ->getMetadataFor($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf(
                    'Could not find metadata for class %s. Error: %s',
                    $entity,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param string $path
     * @param string $bundleName
     *
     * @return string
     */
    protected function getEntityShortcutFromPath($path, $bundleName)
    {
        try {
            //Return if already in shortcut format
            return Validators::validateEntityName($path);
        } catch (\InvalidArgumentException $e) {
            $path = String::create($path);
            $entityName = (string) $path
                ->substr($path->indexOfLast(DIRECTORY_SEPARATOR) + 1, $path->count())
                ->removeRight('.php')
            ;

            return $bundleName . ':' . $entityName;
        }
    }
}
