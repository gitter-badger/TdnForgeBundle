<?php

namespace Tdn\ForgeBundle\Template\Strategy;

use Tdn\PhpTypes\Type\String;

/**
 * Class TwigStrategy
 * @package Tdn\ForgeBundle\Template\Strategy
 */
class TwigStrategy extends AbstractStrategy implements TemplateStrategyInterface
{
    /**
     * @var array|\Twig_SimpleFilter[]
     */
    private $twigFilters;

    /**
     * @param string $template
     * @param array $parameters
     *
     * @throws \Twig_Error_Loader  When the template cannot be found
     * @throws \Twig_Error_Syntax  When an error occurred during compilation
     * @throws \Twig_Error_Runtime When an error occurred during rendering
     *
     * @return string Rendered/Filtered template as a string.
     */
    public function render($template, array $parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->getSkeletonDirs()), [
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ]);

        foreach ($this->getTwigFilters() as $twigFilter) {
            $twig->addFilter($twigFilter);
        }

        return $twig->render($template, $parameters);
    }

    /**
     * Adds the following functions to templates (case-sensitive):
     * - pluralize: Pluralizes current string.
     * - lowerfirst: multi byte-compliant lcfirst
     * - addslashes: php's addslashes function
     *
     * @return array|\Twig_SimpleFilter[]
     */
    protected function getTwigFilters()
    {
        if ($this->twigFilters == null) {
            $this->twigFilters = [
                new \Twig_SimpleFilter(
                    'pluralize',
                    function ($input) {
                        return (string) String::create($input)->pluralize();
                    }
                ),
                new \Twig_SimpleFilter(
                    'underscore',
                    function ($input) {
                        return (string) String::create($input)->underscored();
                    }
                ),
                new \Twig_SimpleFilter(
                    'lowerfirst',
                    function ($input) {
                        return (string) String::create($input)->lowerCaseFirst();
                    }
                ),
                new \Twig_SimpleFilter('addslashes', 'addslashes')
            ];
        }

        return $this->twigFilters;
    }
}
