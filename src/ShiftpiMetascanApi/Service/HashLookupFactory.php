<?php
namespace ShiftpiMetascanApi\Service;

use ShiftpiMetascanApi\Entity\Result;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \ShiftpiMetascanApi\Service\HashLookup
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class HashLookupFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $client = new HttpClient();
        $client->setAdapter($serviceLocator->get('ShiftpiMetascanApi\Http\Adapter'));

        return new HashLookup(
            $serviceLocator->get(Result::class),
            $serviceLocator->get('ShiftpiMetascanApi\Hydrator\Api'),
            $client,
            $serviceLocator->get('ShiftpiMetascanApi\Http\HashLookupRequest')
        );
    }
}