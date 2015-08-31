<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;
use Tdn\ForgeBundle\Writer\Strategy\WriterStrategyInterface;
use Tdn\ForgeBundle\Services\Doctrine\EntityHelper;

/**
 * Class AbstractCommand
 *
 * Basic class that all generator commands can extend.
 *
 * @package Tdn\ForgeBundle\Command
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    /**
     * @return QuestionHelper
     */
    protected function getQuestionHelper()
    {
        return $this->getHelperSet()->get('question');
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @return GeneratorFactoryInterface
     */
    protected function getGeneratorFactory()
    {
        return $this->getContainer()->get('tdn_forge.generator.factory.standard_generator_factory');
    }

    /**
     * @return WriterStrategyInterface
     */
    protected function getWriterStrategy()
    {
        return $this->getContainer()->get('tdn_forge.writer.strategy.default');
    }

    /**
     * @return EntityHelper
     */
    protected function getEntityHelper()
    {
        return $this->getContainer()->get('tdn_forge.doctrine.entity.helper');
    }
}
