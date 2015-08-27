<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Services\Doctrine\EntityHelper;
use Tdn\ForgeBundle\Services\Symfony\ServiceManager;

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return EntityHelper
     */
    protected function getEntityHelper()
    {
        return $this->getContainer()->get('tdn_forge.doctrine.entity.helper');
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceFileUtils()
    {
        return $this->getContainer()->get('tdn_forge.symfony.service.utils');
    }

    /**
     * @return QuestionHelper
     */
    protected function getQuestionHelper()
    {
        return $this->getHelperSet()->get('question');
    }

    /**
     * @param OutputInterface $output
     * @param File $file
     */
    protected function printFileGeneratedMessage(OutputInterface $output, File $file)
    {
        $output->writeln(sprintf(
            'The new <info>%s</info> file has been created under <info>%s</info>.',
            $file->getFilename(),
            $file->getRealPath()
        ));

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf(
                'Contents:' . PHP_EOL . '%s',
                $file->getContents()
            ));
        }
    }
}
