<?php

namespace Tdn\ForgeBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\Format;
use Tdn\ForgeBundle\Model\File;

/**
 * Class AbstractCommand
 * @package Tdn\ForgeBundle\Command
 */
abstract class AbstractGeneratorCommand extends AbstractCommand
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
    private $generatorOptions = [];

    /**
     * @var ArrayCollection|File[]
     */
    private $files;

    /**
     * @return string
     */
    abstract protected function getType();

    /**
     * @param array $generatorOptions
     */
    protected function setGeneratorOptions(array $generatorOptions)
    {
        $this->generatorOptions = $generatorOptions;
    }

    /**
     * @return array
     */
    protected function getGeneratorOptions()
    {
        return $this->generatorOptions;
    }

    /**
     * @return ArrayCollection|File[]
     */
    protected function getFiles()
    {
        if (null === $this->files) {
            $this->files = new ArrayCollection();
        }

        return $this->files;
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
                'Overwrite existing files ' .
                '[Warning: any modifications done to previously generated files will be discarded].'
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

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (($input->getOption('entity') === null && $input->getOption('entities-location') === null) ||
            ($input->getOption('entity') !== null && $input->getOption('entities-location') !== null)
        ) {
            //We have to target something....will implement ask questions later.
            throw new \InvalidArgumentException(
                'Please ensure either option "entity" or "entities-location" have a proper value'
            );
        }

        if ($input->getOption('entities-location') !== null && $input->getOption('bundle-name') === null) {
            //We have to know the bundle name...will implement ask questions later.
            throw new \InvalidArgumentException(
                'Please ensure the bundle name is passed in when using entities location.'
            );
        }

        parent::interact($input, $output);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $entities = $this->resolveEntities($input);
        $overWrite = $input->getOption('overwrite') ? true : false;
        $targetDirectory = $input->hasOption('target-directory') ? $input->getOption('target-directory') : null;
        $format = $input->getOption('format');
        $format = ($format == Format::YML) ? Format::YAML : $format; //normalize.
        $this->files = new ArrayCollection();

        foreach ($entities as $entity) {
            list($bundle, $entity) = $this->parseShortcutNotation($entity);

            $generator = $this->getGeneratorFactory()->create(
                $this->getType(),
                $this->getEntityHelper()->getMetadata($bundle, $entity),
                $bundle,
                $format,
                $targetDirectory,
                $overWrite,
                $this->getGeneratorOptions()
            );

            $files = $generator->generate();
            foreach ($files as $file) {
                $this->files->add($file);
            }

            $generator->getMessages()->forAll(function ($message) use ($output) {
                $output->writeln("<info>$message</info>");
            });
        }

        parent::initialize($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->getFiles();
        if ($this->shouldContinue($input, $output, $files)) {
            $output->writeln("<info>Generating files...this could take a while.</info>");
            foreach ($files as $file) {
                $this->getWriterStrategy()->writeFile($file);
                $output->writeln(sprintf(
                    'The new <info>%s</info> file has been created under <info>%s</info>.',
                    $file->getFilename(),
                    $file->getRealPath()
                ));
            }
        } else {
            $output->writeln('<notice>Generation cancelled.</notice>');

            return 1;
        }

        return 0;
    }

    /**
     * Confirms disk write.
     *
     * If input is interactive asks to confirm writing files to disk.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param ArrayCollection $files
     *
     * @return bool
     */
    protected function shouldContinue(InputInterface $input, OutputInterface $output, ArrayCollection $files)
    {
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion(
                sprintf(
                    'File(s) to be modified/generated:' .
                    PHP_EOL . '<info>%s</info>' .
                    PHP_EOL . 'Do you confirm generation/modification of the files listed above (y/n)?',
                    implode(PHP_EOL, $files->map(function (File $file) {
                        return '- ' . $file->getRealPath();
                    })->toArray())
                ),
                false
            );

            return (bool) $this->getQuestionHelper()->ask($input, $output, $question);
        }

        return true;
    }

    /**
     * Returns an array containing the bundle and the entity.
     *
     * @param string $shortcut
     *
     * @return array containing two elements [0] => BundleInterface, [1] => (string) Entity
     */
    private function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The entity name must contain a ":".' .
                    ' "%s" given, expecting something like "AcmeBlogBundle:Blog/Post"',
                    $entity
                )
            );
        }

        return array($this->getKernel()->getBundle(substr($entity, 0, $pos)), substr($entity, $pos + 1));
    }

    /**
     * @param InputInterface $input
     *
     * @return ArrayCollection|string
     */
    private function resolveEntities(InputInterface $input)
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

        return $entities;
    }
}
