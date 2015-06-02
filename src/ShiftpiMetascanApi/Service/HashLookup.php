<?php
namespace ShiftpiMetascanApi\Service;

use ShiftpiMetascanApi\Entity\Result;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Decoder;
use Zend\Json\Json;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Http\Response;

/**
 * Service for scanning data via its hash
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class HashLookup
{
    /** @var Result */
    protected $resultPrototype;

    /** @var HydratorInterface */
    protected $fileHydrator;

    /** @var Request */
    protected $requestPrototype;

    /** @var Client */
    protected $httpClient;

    /** @var Request */
    protected $hashLookupRequestPrototype;

    /**
     * @param Result $resultPrototype
     * @param HydratorInterface $fileHydrator
     * @param Client $httpClient
     * @param Request $hashLookupRequestPrototype
     */
    public function __construct(
        Result $resultPrototype,
        HydratorInterface $fileHydrator,
        Client $httpClient,
        Request $hashLookupRequestPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->fileHydrator = $fileHydrator;
        $this->httpClient = $httpClient;
        $this->hashLookupRequestPrototype = $hashLookupRequestPrototype;
    }

    /**
     * Check whether the hash is known as one of an infected file
     * @param $hash
     * @return Result
     * @throws \InvalidArgumentException
     * @throws RequestFailedException
     */
    public function lookup($hash)
    {
        // Check if $hash is MD5, SHA1 or SHA256
        if (strlen($hash) !== 32 && strlen($hash) !== 40 && strlen($hash) !== 64) {
            throw new \InvalidArgumentException('Hash must be of type MD5, SHA1 or SHA256');
        }

        // It seems, that the API transforms every hash to upper characters
        $hash = strtoupper($hash);

        $request = clone $this->hashLookupRequestPrototype;
        $request->setUri($request->getUriString() . '/' . $hash);
        $httpResponse = $this->httpClient->send($request);

        if ($httpResponse->getStatusCode() !== Response::STATUS_CODE_200) {
            throw new RequestFailedException($httpResponse->getStatusCode());
        }

        /** @var Result $result */
        $result = clone $this->resultPrototype;
        $data = Decoder::decode($httpResponse->getContent(), Json::TYPE_ARRAY);

        if (isset($data[$hash]) && $data[$hash] === 'Not Found') {
            $result->setResult(Result::RESULT_NOTFOUND);
            $result->setRawResult($data);
            return $result;
        }

        $result = $this->fileHydrator->hydrate($data, $result);
        $result = $this->fileHydrator->hydrate($data['scan_results'], $result);
        $result = $this->fileHydrator->hydrate($data['file_info'], $result);
        $result->setRawResult($data);

        return $result;
    }
}