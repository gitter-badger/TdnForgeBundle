<?php

namespace Foo\BarBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Foo\BarBundle\Exception\InvalidFormException;
use Foo\BarBundle\Form\Type\FooType;
use Foo\BarBundle\Entity\Manager\FooManager;
use Foo\BarBundle\Entity\FooInterface;

/**
 * @DI\Service("foo_bar.handler.foo_handler")
 *
 * Class FooHandler
 * @package Foo\BarBundle\Handler
 */
class FooHandler extends FooManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine"),
     *     "class" = "Foo\BarBundle\Entity\Foo",
     *     "formFactory" = @DI\Inject("form.factory")
     * })
     *
     * @param Registry $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Registry $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return FooInterface
     */
    public function post(array $parameters)
    {
        $foo = $this->createFoo();

        return $this->processForm($foo, $parameters, 'POST');
    }

    /**
     * @param FooInterface $foo
     * @param array $parameters
     *
     * @return FooInterface
     */
    public function put(FooInterface $foo, array $parameters)
    {
        return $this->processForm(
            $foo,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param FooInterface $foo
     * @param array $parameters
     *
     * @return FooInterface
     */
    public function patch(FooInterface $foo, array $parameters)
    {
        return $this->processForm(
            $foo,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param FooInterface $foo
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return FooInterface
     */
    protected function processForm(FooInterface $foo, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(
            new FooType(),
            $foo,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $foo = $form->getData();
            $this->updateFoo(
                $foo,
                true,
                ('PUT' === $method || 'PATCH' === $method)
            );

            return $foo;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
