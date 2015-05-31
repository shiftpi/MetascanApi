<?php
namespace ShiftpiMetascanApi\Service;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client\Adapter\Curl;
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
        $client = new HttpClient();
        $client->setAdapter(new Curl());

        $fileHydrator = new ClassMethods();
        $fileHydrator->setNamingStrategy(new MapNamingStrategy([
            'file_id' => 'fileId',
            'scan_all_result_i' => 'result',
            'file_type_category' => 'fileTypeCategory',
            'total_avs' => 'totalAvs',
            'progress_percentage' => 'percCompleted',
        ]));

        $adapter = new Curl();
        $adapter->setCurlOption(CURLOPT_TIMEOUT, 5 * 60);

        $client = new HttpClient();
        $client->setAdapter($adapter);

        return new Scan(
            $serviceLocator->get(Result::class),
            $fileHydrator,
            new Request(),
            new ClassMethods(),
            $serviceLocator->get(Progress::class),
            $client,
            $serviceLocator->get('ShiftpiMetascanApi\Http\ScanRequest')
        );
    }
}