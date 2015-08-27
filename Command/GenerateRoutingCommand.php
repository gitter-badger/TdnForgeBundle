<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;

/**
 * Class GenerateRoutingCommand
 *
 * Adds routes from the routing file based on an entity.
 *
 * @package Tdn\SfRoutingGeneratorBundle\Command
 */
class GenerateRoutingCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const DEFAULT_ROUTING = 'routing';

    /**
     * @var string
     */
    const NAME = 'forge:generate:routing';

    /**
     * @var string
     */
    const DESCRIPTION =
        'Adds a routing entry for a rest controller based on an entity.';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'routing-file',
                InputArgument::OPTIONAL,
                'The routing file, defaults to: ' . self::DEFAULT_ROUTING . '.' . self::DEFAULT_FORMAT,
                self::DEFAULT_ROUTING
            )
            ->addOption(
                'prefix',
                null,
                InputOption::VALUE_REQUIRED,
                'The route prefix'
            )
        ;

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->setOptions([
            'routing-file' => $input->getArgument('routing-file'),
            'prefix' => $input->getOption('prefix')
        ]);

        parent::interact($input, $output);
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return GeneratorFactoryInterface::TYPE_ROUTING_GENERATOR;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Routing config'];
    }
}
