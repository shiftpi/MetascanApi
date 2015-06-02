<?php
namespace ShiftpiMetascanApi\Http;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HashLookupRequestFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        $request = $serviceLocator->get('ShiftpiMetascanApi\Http\ApiRequest');
        $request->setUri($config['metascan']['hash_url']);

        return $request;
    }
}