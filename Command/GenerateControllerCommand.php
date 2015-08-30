<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tdn\ForgeBundle\Generator\Factory\GeneratorFactoryInterface;
use Tdn\PhpTypes\Type\String;

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
                'with-tests',
                null,
                InputOption::VALUE_NONE,
                'Use flag to generate standard CRUD tests. ' .
                'Requires doctrine/alice fixtures to be present. Requires LiipFunctionalTestBundle.'
            )
            ->addOption(
                'prefix',
                null,
                InputOption::VALUE_OPTIONAL,
                'When using annotations you could also add this value (e.g. "v1" or "api") to the url as a prefix.'
            )
            ->addOption(
                'fixtures-path',
                null,
                InputOption::VALUE_OPTIONAL,
                'When generating tests, the location of your fixtures is required.' .
                'Supported fixture types are: Alice, Doctrine (e.g. objects implementing FixtureInterface)'
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

        return (string) String::create($prefix)->removeLeft('/');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throw \InvalidArgumentException when options mismatch.
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('with-tests') && !$input->getOption('fixtures-path')) {
            throw new \InvalidArgumentException('Fixtures path is required when enabling test generation.');
        }

        $this->setGeneratorOptions([
            'swagger'       => ($input->getOption('with-swagger') ? true : false),
            'tests'         => ($input->getOption('with-tests') ? true : false),
            'prefix'        => $this->getRoutePrefix($input->getOption('prefix')),
            'fixtures-path' => ($input->getOption('fixtures-path') ? $input->getOption('fixtures-path') : '')
        ]);

        parent::initialize($input, $output);
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return GeneratorFactoryInterface::TYPE_CONTROLLER_GENERATOR;
    }
}
