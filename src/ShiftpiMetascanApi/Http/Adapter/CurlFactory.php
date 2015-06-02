<?php
namespace ShiftpiMetascanApi\Http\Adapter;

use Zend\Http\Client\Adapter\Curl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for HTTP client adapter with some configuration
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class CurlFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = new Curl();
        $adapter->setCurlOption(CURLOPT_TIMEOUT, 5 * 60);                   // Scanning may take some time

        return $adapter;
    }
}