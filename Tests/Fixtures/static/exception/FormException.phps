<?php

namespace Foo\BarBundle\Exception;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\Common\Collections\ArrayCollection;
use Excalibur\AppBundle\Form\Violation\ArrayCollectionMapper;

/**
 * Class InvalidFormException
 * @package Foo\BarBundle\Exception
 */
class InvalidFormException extends BadRequestHttpException
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @param string $message
     * @param FormInterface $form
     * @param \Exception|null $previous
     * @param int $code
     */
    public function __construct($message, FormInterface $form, \Exception $previous = null, $code = 0)
    {
        $this->form = $form;
        parent::__construct($message, $previous, $code);
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array|ArrayCollection
     */
    public function getErrors()
    {
        return (new ArrayCollectionMapper())->getErrors($this->form);
    }
}
