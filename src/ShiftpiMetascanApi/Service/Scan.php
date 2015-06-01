<?php

namespace ShiftpiMetascanApi\Service;

use ShiftpiMetascanApi\Entity\Result;
use ShiftpiMetascanApi\Entity\Progress;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Header;
use Zend\Http\Response;
use Zend\Json\Decoder;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Service for scanning data by uploading the whole file
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class Scan
{
    /** @var Result */
    protected $resultPrototype;

    /** @var HydratorInterface */
    protected $fileHydrator;

    /** @var Request */
    protected $requestPrototype;

    /** @var HydratorInterface */
    protected $progressHydrator;

    /** @var Progress */
    protected $progressPrototype;

    /** @var Client */
    protected $httpClient;

    /** @var Request */
    protected $scanRequestPrototype;

    /**
     * @param Result $resultPrototype
     * @param HydratorInterface $fileHydrator
     * @param Request $requestPrototype
     * @param HydratorInterface $progressHydrator
     * @param Progress $progressPrototype
     * @param Client $httpClient
     * @param Request $scanRequestPrototype
     */
    public function __construct(
        Result $resultPrototype,
        HydratorInterface $fileHydrator,
        Request $requestPrototype,
        HydratorInterface $progressHydrator,
        Progress $progressPrototype,
        Client $httpClient,
        Request $scanRequestPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->fileHydrator = $fileHydrator;
        $this->requestPrototype = $requestPrototype;
        $this->progressHydrator = $progressHydrator;
        $this->progressPrototype = $progressPrototype;
        $this->httpClient = $httpClient;
        $this->scanRequestPrototype = $scanRequestPrototype;
    }

    /**
     * Scan synchronously
     * @param string $data Data that has to be scanned
     * @param string $filename Optional, filename
     * @param string $archivepassword Optional, archive password, if $data is an encrypted archive
     * @return Result
     * @throws RequestFailedException
     */
    public function scan($data, $filename = '', $archivepassword = '')
    {
        $startRequest = $this->getStartScanRequest($data, [
            'filename' => $filename,
            'archivepwd' => $archivepassword,
        ]);
        $httpResponse = $this->httpClient->send($startRequest);

        if ($httpResponse->getStatusCode() !== Response::STATUS_CODE_200) {
            throw new RequestFailedException($httpResponse->getStatusCode());
        }

        /** @var Progress $progress */
        $progress = $this->progressHydrator->hydrate(
            Decoder::decode($httpResponse->getContent(), Json::TYPE_ARRAY),
            $this->progressPrototype
        );

        $scanResult = clone $this->resultPrototype;

        do {
            usleep(500);                               // Give the API some time

            $progressRequest = $this->getProgressScanRequest($progress->getDataId(), $progress->getRestIp());
            $progressResponse = $this->httpClient->send($progressRequest);

            if ($progressResponse->getStatusCode() !== Response::STATUS_CODE_200) {
                throw new RequestFailedException($progressResponse->getStatusCode());
            }

            if (isset($content[$progress->getDataId()]) && $content[$progress->getDataId()] === 'Not Found') {
                throw new RequestFailedException(RequestFailedException::REASON_NOTFOUND);
            }

            $content = Decoder::decode($progressResponse->getContent(), Json::TYPE_ARRAY);

            /** @var Result $scanResult */
            $scanResult = $this->fileHydrator->hydrate($content['scan_results'], $scanResult);
            if ($scanResult->getPercCompleted() === 100) {
                $scanResult = $this->fileHydrator->hydrate($content, $scanResult);
                $scanResult = $this->fileHydrator->hydrate($content['file_info'], $scanResult);
                $scanResult->setRawResult($content);
            }
        } while ($scanResult->getPercCompleted() < 100);

        return $scanResult;
    }

    /**
     * Returns an "start the scan!"-request
     * @param string $data
     * @param array $customHeaders
     * @return Request
     */
    protected function getStartScanRequest($data, array $customHeaders)
    {
        $request = clone $this->scanRequestPrototype;

        $request->getHeaders()->addHeaders($customHeaders);
        $request->setContent($data);
        $request->setMethod(Request::METHOD_POST);

        return $request;
    }

    /**
     * Returns an "what is the current status?"-request
     * @param string $dataId
     * @param string $restIp
     * @return Request
     */
    protected function getProgressScanRequest($dataId, $restIp)
    {
        $request = clone $this->scanRequestPrototype;

        $request->setUri($request->getUriString() . '/' . $dataId);
        $request->getUri()->setQuery([
            'data_id' => $dataId,
            'rest_ip' => $restIp
        ]);

        return $request;
    }
}