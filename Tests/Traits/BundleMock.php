<?php

namespace Tdn\ForgeBundle\Tests\Traits;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use \Mockery;

/**
 * Class BundleMock
 * @package Tdn\ForgeBundle\Tests\Traits
 */
trait BundleMock
{
    /**
     * @var BundleInterface
     */
    protected $bundle;

    /**
     * @return BundleInterface
     */
    protected function getBundle()
    {
        if (null === $this->bundle) {
            $this->bundle = $this->createBundle();
        }

        return $this->bundle;
    }

    /**
     * @param string $outDir
     *
     * @return BundleInterface
     */
    private function createBundle($outDir = '')
    {
        $bundle = Mockery::mock('\Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle
            ->shouldDeferMissing()
            ->shouldReceive(
                [
                    'getPath' => $outDir,
                    'getNamespace' => 'Foo\\BarBundle',
                    'getName' => 'FooBarBundle'
                ]
            )
            ->zeroOrMoreTimes()
        ;

        return $bundle;
    }
}
