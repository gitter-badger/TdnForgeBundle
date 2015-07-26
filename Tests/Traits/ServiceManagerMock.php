<?php

namespace Tdn\ForgeBundle\Tests\Traits;

use Tdn\ForgeBundle\Services\Symfony\ServiceManager;
use \Mockery;

/**
 * Class ServiceManagerMock
 * @package Tdn\ForgeBundle\Tests\Traits
 */
trait ServiceManagerMock
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        if (null === $this->serviceManager) {
            $this->serviceManager = $this->createServiceManager();
        }

        return $this->serviceManager;
    }

    /**
     * @return ServiceManager
     */
    private function createServiceManager()
    {
        $serviceManager = Mockery::mock(new ServiceManager());
        $serviceManager->shouldDeferMissing();

        return $serviceManager;
    }
}
