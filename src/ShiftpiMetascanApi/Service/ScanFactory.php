<?php
namespace ShiftpiMetascanApi\Service;

use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ShiftpiMetascanApi\Entity\Result;
use ShiftpiMetascanApi\Entity\Progress;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Factory for \ShiftpiMetascanApi\Service\Scan
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class ScanFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $client = new HttpClient();
        $client->setAdapter($serviceLocator->get('ShiftpiMetascanApi\Http\Adapter'));

        return new Scan(
            $serviceLocator->get(Result::class),
            $serviceLocator->get('ShiftpiMetascanApi\Hydrator\Api'),
            new ClassMethods(),
            $serviceLocator->get(Progress::class),
            $client,
            $serviceLocator->get('ShiftpiMetascanApi\Http\ScanRequest')
        );
    }
}