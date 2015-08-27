<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;

/**
 * Class GenerateControllerCommand
 *
 * Generates a CRUD controller based on an entity.
 *
 * @package Tdn\ForgeBundle\Command
 */
class GenerateControllerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'forge:generate:controller';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates a Restful controller based on a doctrine entity.';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption(
                'with-swagger',
                null,
                InputOption::VALUE_NONE,
                'Use NelmioApiDocBundle (which uses swagger-ui) to document the controller'
            )
            ->addOption(
                'prefix',
                null,
                InputOption::VALUE_OPTIONAL,
                'If using annotations, you should also add a prefix (e.g. v1) to the controller ' .
                'if you want to version the api.'
            )
            ->addOption(
                'with-tests',
                null,
                InputOption::VALUE_NONE,
                'Use flag to generate standard CRUD tests. ' .
                'Requires doctrine fixtures to be present. Specifications in Readme.'
            )
        ;

        parent::configure();
    }

    /**
     * Gets the route prefix for the resource
     *
     * Gets a route prefix to use when using annotations. Otherwise the route prefix
     * is set through the `RoutingGenerator`.
     *
     * @param  string $routePrefix
     *
     * @return string
     */
    protected function getRoutePrefix($routePrefix = '')
    {
        $prefix = (!empty($routePrefix)) ? $routePrefix: '';

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->setOptions([
            'swagger' => ($input->getOption('with-swagger') ? true : false),
            'prefix'  => $this->getRoutePrefix($input->getOption('prefix')),
            'tests'   => ($input->getOption('with-tests') ? true : false)
        ]);

        parent::interact($input, $output);
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return GeneratorFactoryInterface::TYPE_CONTROLLER_GENERATOR;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Rest Controller'];
    }
}
