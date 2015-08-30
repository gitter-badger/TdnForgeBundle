<?php

namespace Tdn\ForgeBundle\Tests\Writer\PostProcessor;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\ForgeBundle\Model\File;
use Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorChain;
use Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorInterface;
use \Mockery;

/**
 * Class PostProcessorChainTest
 * @package Tdn\ForgeBundle\Tests\Writer\PostProcessor
 */
class PostProcessorChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PostProcessorChain
     */
    private $postProcessorChain;

    protected function setUp()
    {
        $this->postProcessorChain = new PostProcessorChain();
    }

    public function testProcessorsForFile()
    {
        $postProcessor = $this->getPostProcessorMock();
        $this->postProcessorChain->addPostProcessor($postProcessor);
        $this->assertContains(
            $postProcessor,
            $this->postProcessorChain->getPostProcessorsForFile($this->getFileMock())
        );
    }

    /**
     * @return File
     */
    private function getFileMock()
    {
        $file = Mockery::mock('\Tdn\ForgeBundle\Model\File');
        $file->shouldIgnoreMissing();

        return $file;
    }

    /**
     * @return PostProcessorInterface
     */
    private function getPostProcessorMock()
    {
        $postProcessor = Mockery::mock('\Tdn\ForgeBundle\Writer\PostProcessor\PostProcessorInterface');
        $postProcessor
            ->shouldIgnoreMissing()
            ->shouldReceive([
                'supports' => true,
                'isValid' => true
            ])
            ->zeroOrMoreTimes()
        ;

        return $postProcessor;
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}
