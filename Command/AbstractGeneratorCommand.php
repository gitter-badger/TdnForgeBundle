<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Generator\Factory\StandardGeneratorFactory;
use Tdn\ForgeBundle\Generator\GeneratorInterface;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\File;

/**
 * Class AbstractCommand
 * @package Tdn\ForgeBundle\Command
 */
abstract class AbstractGeneratorCommand extends AbstractCommand implements GeneratorCommandInterface
{
    /**
     * Override in child class
     * @var string
     */
    const NAME = '';

    /**
     * Override in child class
     * @var string
     */
    const DESCRIPTION = '';

    /**
     * @var string
     */
    const DEFAULT_FORMAT = Format::YAML;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @return string[]
     */
    abstract protected function getFiles();

    /**
     * @return string
     */
    abstract protected function getType();

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param GeneratorInterface $generator
     */
    public function setGenerator(GeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return GeneratorInterface
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        if (static::NAME == '' || static::DESCRIPTION == '') {
            throw new \LogicException(
                'Please set the name and description of the command. Error in: ' . get_called_class()
            );
        }

        $this
            ->addOption(
                'entity',
                null,
                InputOption::VALUE_OPTIONAL,
                'The entity class name to initialize (shortcut notation: FooBarBundle:Entity)'
            )
            ->addOption(
                'entities-location',
                null,
                InputOption::VALUE_OPTIONAL,
                'The directory containing the entity classes to target'
            )
            ->addOption(
                'overwrite',
                null,
                InputOption::VALUE_NONE,
                'Overwrite existing ' . implode(',', $this->getFiles())
            )
            ->addOption(
                'target-directory',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify a different target directory (namespaces will have to be changed manually)'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_OPTIONAL,
                'The service file format (yaml, xml, annotations). default: yaml',
                self::DEFAULT_FORMAT
            )
            ->addOption(
                'exclude',
                null,
                (InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY),
                'File-names to exclude when generating files (with --entities-location). ' .
                'No ext required. Case insensitive',
                []
            )
            ->addOption(
                'bundle-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of the bundle. Defaults to magic find based on directory of entity/entities.'
            )
            ->addOption(
                'no-postprocessing',
                null,
                InputOption::VALUE_OPTIONAL,
                'The name of the bundle. Defaults to magic find based on directory of entity/entities.'
            )
            ->setDescription(static::DESCRIPTION)
            ->setName(static::NAME)
        ;
    }

    /**
     * Generates the files based on the options provided.
     *
     * Sets up all dependencies for generator, prepares it
     * and ultimately tells it to generate it's files.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (true !== $error = $this->isInputValid($input)) {
            $output->writeln("<error>$error</error>");

            return 1;
        }

        $entities = $this->resolveEntities($input, $output);
        $overWrite = $input->getOption('overwrite') ? true : false;
        $targetDirectory = $input->hasOption('target-directory') ? $input->getOption('target-directory') : null;
        $format = $input->getOption('format');
        $format = ($format == Format::YML) ? Format::YAML : $format; //normalize.

        foreach ($entities as $entity) {
            list($bundleName, $entity) = $this->parseShortcutNotation($entity);
            $bundle  = $this->getKernel()->getBundle($bundleName);

            if (null == $generator = $this->getGenerator()) {
                $generator = $this->getGeneratorFactory()->create(
                    $this->getType(),
                    $this->getEntityHelper()->getMetadata($bundle, $entity),
                    $bundle,
                    $format,
                    $targetDirectory,
                    $overWrite,
                    $this->getOptions()
                );
            }

            if ($this->shouldContinue($input, $output, $generator->getFiles(), $entity)) {
                $output->write(PHP_EOL);
                $output->writeln("<info>Generating files...this could take a while.</info>");
                foreach ($generator->generate() as $file) {
                    $this->printFileGeneratedMessage($output, $file);
                }
                $output->write(PHP_EOL);
                foreach ($generator->getMessages() as $message) {
                    $output->writeln(
                        sprintf(
                            '<comment>%s</comment>',
                            $message
                        )
                    );
                }
                $output->write(PHP_EOL);
            } else {
                $output->writeln('<notice>Generation cancelled.</notice>');

                return 1;
            }
        }

        return 0;
    }

    /**
     * Confirms generation
     *
     * If input is interactive asks to confirm generation of files. You have to explicitly
     * confirm to continue.
     * If input is **not** interactive, then it will automatically return true.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param ArrayCollection $generatedFiles
     * @param string          $entity
     *
     * @return bool
     */
    protected function shouldContinue(
        InputInterface $input,
        OutputInterface $output,
        ArrayCollection $generatedFiles,
        $entity
    ) {
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion(
                sprintf(
                    'Entity %s - File(s) to be modified:' .
                    PHP_EOL . '<info>%s</info>' .
                    PHP_EOL . 'Do you confirm generation/manipulation of the files listed above (y/n)?',
                    $entity,
                    implode(PHP_EOL, $generatedFiles->map(function (File $generatedFile) {
                        return '- ' . $generatedFile->getRealPath();
                    })->toArray())
                ),
                false
            );

            return (bool) $this->getQuestionHelper()->ask($input, $output, $question);
        }

        return true;
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @return StandardGeneratorFactory
     */
    protected function getGeneratorFactory()
    {
        return $this->getContainer()->get('tdn_forge.generator.factory.standard_generator_factory');
    }

    /**
     * @param InputInterface $input
     *
     * @return mixed Returns true if valid, string containing error if not.
     */
    protected function isInputValid(InputInterface $input)
    {
        if (($input->getOption('entity') === null && $input->getOption('entities-location') === null) ||
            ($input->getOption('entity') !== null && $input->getOption('entities-location') !== null)
        ) {
            return 'Please use either entity OR entities-location. One is required.';
        }

        if ($input->getOption('entities-location') !== null && $input->getOption('bundle-name') === null) {
            return 'Bundle Name parameter is required when loading many entities.';
        }

        return true;
    }

    /**
     * Returns an array containing the bundle and the entity.
     *
     * @param string $shortcut
     *
     * @return string[]
     */
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)',
                    $entity
                )
            );
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return ArrayCollection|string
     */
    private function resolveEntities(InputInterface $input, OutputInterface $output)
    {
        $entities = (null === $input->getOption('entities-location')) ?
            new ArrayCollection() :
            $this->getEntityHelper()->getClassesInDirectory(
                $input->getOption('entities-location'),
                $input->getOption('bundle-name'),
                $input->getOption('exclude')
            )
        ;

        if (null !== $entity = $input->getOption('entity')) {
            $entity = Validators::validateEntityName($entity);
            $entities->add($entity);
        }

        $output->writeln(
            sprintf(
                'Generating files for %s entit%s... (You can skip interaction by passing in the --no-interaction flag)',
                $entities->count(),
                $entities->count() > 1 ? 'ies' : 'y'
            )
        );

        return $entities;
    }
}
