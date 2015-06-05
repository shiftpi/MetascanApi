<?php
namespace ShiftpiMetascanApi\Hydrator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            'total_avs' => 'scannerCount',
            'total_time' => 'scanTime',
        ]));

        return $hydrator;
    }
}