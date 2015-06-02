<?php
namespace ShiftpiMetascanApi\Service;

use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ShiftpiMetascanApi\Entity\Result;
use ShiftpiMetascanApi\Entity\Progress;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

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

        return new Scan(
            $serviceLocator->get(Result::class),
            $fileHydrator,
            new ClassMethods(),
            $serviceLocator->get(Progress::class),
            $client,
            $serviceLocator->get('ShiftpiMetascanApi\Http\ScanRequest')
        );
    }
}