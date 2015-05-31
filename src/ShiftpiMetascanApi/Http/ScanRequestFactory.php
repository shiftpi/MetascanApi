<?php
namespace ShiftpiMetascanApi\Http;

use Zend\Http\Header;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating a API scan request
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class ScanRequestFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        $request = $serviceLocator->get('ShiftpiMetascanApi\Http\ApiRequest');
        $request->setUri($config['metascan']['data_url']);

        return $request;
    }
}