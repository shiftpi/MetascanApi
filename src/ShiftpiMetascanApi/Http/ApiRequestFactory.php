<?php
namespace ShiftpiMetascanApi\Http;

use Zend\Http\Request;
use Zend\Http\Headers;
use Zend\Http\Header;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating a standard API HTTP request
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class ApiRequestFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        $headers = new Headers();
        $headers->addHeaders([
            Header\Accept::fromString('application/json'),
            Header\AcceptCharset::fromString('UTF-8'),
            'apikey' => $config['metascan']['key'],
        ]);

        $request = new Request();
        $request->setHeaders($headers);

        return $request;
    }
}