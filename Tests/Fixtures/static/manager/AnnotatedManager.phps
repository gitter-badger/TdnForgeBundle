<?php

namespace Foo\BarBundle\Entity\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\Common\Collections\ArrayCollection;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Auto-generated for type-hinting and auto-completion purposes.
 *
 * @DI\Service("foo_bar.entity.manager.foo_manager")
 *
 * Class FooManager
 * @package Foo\BarBundle\Entity\Manager
 */
class FooManager extends AbstractManager implements FooManagerInterface
{
    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine"),
     *     "class" = "Foo\BarBundle\Entity\Foo",
     *     "formFactory" = @DI\Inject("form.factory")
     * })
     *
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return FooInterface
     */
    public function findFooBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|FooInterface[]
     */
    public function findFoosBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findAllBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param FooInterface $foo
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateFoo(FooInterface $foo, $andFlush = true, $forceId = false)
    {
        parent::update($foo, $andFlush, $forceId);
    }

    /**
     * @param FooInterface $foo
     */
    public function deleteFoo(FooInterface $foo)
    {
        parent::delete($foo);
    }

    /**
     * @return FooInterface
     */
    public function createFoo()
    {
        $class = $this->getClass();
        return new $class();
    }
}
