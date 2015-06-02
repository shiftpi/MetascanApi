<?php
namespace ShiftpiMetascanApi\Service;

use ShiftpiMetascanApi\Entity\Result;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

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
        $fileHydrator = new ClassMethods();
        $fileHydrator->setNamingStrategy(new MapNamingStrategy([
            'file_id' => 'fileId',
            'scan_all_result_i' => 'result',
            'file_type_category' => 'fileTypeCategory',
            'total_avs' => 'totalAvs',
            'progress_percentage' => 'percCompleted',
        ]));

        $client = new HttpClient();
        $client->setAdapter($serviceLocator->get('ShiftpiMetascanApi\Http\Adapter'));

        return new HashLookup(
            $serviceLocator->get(Result::class),
            $fileHydrator,
            $client,
            $serviceLocator->get('ShiftpiMetascanApi\Http\HashLookupRequest')
        );
    }
}