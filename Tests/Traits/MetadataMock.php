<?php

namespace Tdn\ForgeBundle\Tests\Traits;

use Doctrine\ORM\Mapping\ClassMetadata;
use \Mockery;

/**
 * Class MetadataMock
 * @package Tdn\ForgeBundle\Tests\Traits
 */
trait MetadataMock
{
    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @return ClassMetadata
     */
    public function getMetadata()
    {
        if (null === $this->metadata) {
            $this->metadata = $this->createMetadata();
        }

        return $this->metadata;
    }

    /**
     * @return ClassMetadata
     */
    private function createMetadata()
    {
        $metadata = Mockery::mock('\Doctrine\ORM\Mapping\ClassMetadata');
        $metadata
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'isIdentifierNatural' => true,
                    'getReflectionClass'  => new \ReflectionClass(new \stdClass())
                ]
            )
            ->zeroOrMoreTimes()
        ;

        if ($metadata instanceof ClassMetadata) {
            $metadata->name       = 'Foo\BarBundle\Entity\Foo';
            $metadata->identifier = ['id'];
            $metadata->associationMappings = [];
            $metadata->namespace = 'Foo\BarBundle\Entity';
            $metadata->fieldMappings = [
                'id' => [
                    'fieldName'  => 'id',
                    'type'       => 'integer',
                    'columnName' => 'id',
                    'id'         => true
                ],
                'description' => [
                    'fieldName'  => 'description',
                    'type'       => 'string',
                    'columnName' => 'description'
                ],
                'name' => [
                    'fieldName'  => 'name',
                    'type'       => 'string',
                    'columnName' => 'name'
                ],
                'title' => [
                    'fieldName'  => 'title',
                    'type'       => 'string',
                    'columnName' => 'title'
                ]
            ];
        }

        return $metadata;
    }
}
