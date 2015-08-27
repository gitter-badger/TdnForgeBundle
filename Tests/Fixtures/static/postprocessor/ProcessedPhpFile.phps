<?php

namespace Foo\BarBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Foo\BarBundle\Exception\InvalidFormException;
use Foo\BarBundle\Form\Type\MegaSuperExtremelyRidiculouslyLongNameType;
use Foo\BarBundle\Entity\Manager\MegaSuperExtremelyRidiculouslyLongNameManager;
use Foo\BarBundle\Entity\MegaSuperExtremelyRidiculouslyLongNameInterface;

/**
 * Class MegaSuperExtremelyRidiculouslyLongNameHandler
 * @package Foo\BarBundle\Handler
 */
class ProcessedFile extends MegaSuperExtremelyRidiculouslyLongNameManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
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
     * @return MegaSuperExtremelyRidiculouslyLongNameInterface
     */
    public function post(array $parameters)
    {
        $megaSuperExtremelyRidiculouslyLongName = $this->createMegaSuperExtremelyRidiculouslyLongName();

        return $this->processForm($megaSuperExtremelyRidiculouslyLongName, $parameters, 'POST');
    }

    /**
     * @param MegaSuperExtremelyRidiculouslyLongNameInterface $megaSuperExtremelyRidiculouslyLongName
     * @param array $parameters
     *
     * @return MegaSuperExtremelyRidiculouslyLongNameInterface
     */
    public function put(
        MegaSuperExtremelyRidiculouslyLongNameInterface $megaSuperExtremelyRidiculouslyLongName,
        array $parameters
    ) {
        return $this->processForm(
            $megaSuperExtremelyRidiculouslyLongName,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param MegaSuperExtremelyRidiculouslyLongNameInterface $megaSuperExtremelyRidiculouslyLongName
     * @param array $parameters
     *
     * @return MegaSuperExtremelyRidiculouslyLongNameInterface
     */
    public function patch(
        MegaSuperExtremelyRidiculouslyLongNameInterface $megaSuperExtremelyRidiculouslyLongName,
        array $parameters
    ) {
        return $this->processForm(
            $megaSuperExtremelyRidiculouslyLongName,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param MegaSuperExtremelyRidiculouslyLongNameInterface $megaSuperExtremelyRidiculouslyLongName
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return MegaSuperExtremelyRidiculouslyLongNameInterface
     */
    protected function processForm(
        MegaSuperExtremelyRidiculouslyLongNameInterface $megaSuperExtremelyRidiculouslyLongName,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new MegaSuperExtremelyRidiculouslyLongNameType(),
            $megaSuperExtremelyRidiculouslyLongName,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
