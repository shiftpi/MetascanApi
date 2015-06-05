<?php
namespace ShiftpiMetascanApi\Hydrator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

/**
 * Factory for creating the default response Hydrator
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class ApiFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = new ClassMethods();
        $hydrator->setNamingStrategy(new MapNamingStrategy([
            'file_id' => 'fileId',
            'scan_all_result_i' => 'result',
            'file_type_category' => 'fileTypeCategory',
            'total_avs' => 'totalAvs',
            'progress_percentage' => 'percCompleted',
            'display_name' => 'displayName',
            'sha256' => 'dataHash',
            'file_type_extension' => 'extension',
            'total_time' => 'scanTime',
        ]));

        return $hydrator;
    }
}