<?php

namespace Foo\BarBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Interface FooManagerInterface
 * @package Foo\BarBundle\Entity\Manager
 */
interface FooManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return FooInterface
     */
    public function findFooBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|FooInterface[]
     */
    public function findFoosBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param FooInterface $foo
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateFoo(FooInterface $foo, $andFlush = true, $forceId = false);

    /**
     * @param FooInterface $foo
     *
     * @return void
     */
    public function deleteFoo(FooInterface $foo);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return FooInterface
     */
    public function createFoo();
}
