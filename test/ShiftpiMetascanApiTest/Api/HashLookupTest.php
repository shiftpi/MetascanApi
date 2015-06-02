<?php
namespace ShiftpiMetascanApiTest\Api;

use ShiftpiMetascanApi\Entity\Result;
use ShiftpiMetascanApi\Http\Adapter\CurlFactory;
use ShiftpiMetascanApi\Http\ApiRequestFactory;
use ShiftpiMetascanApi\Http\HashLookupRequestFactory;
use ShiftpiMetascanApi\Service\HashLookup;
use ShiftpiMetascanApi\Service\HashLookupFactory;
use ShiftpiMetascanApi\Service\RequestFailedException;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests the HashLookup class against the API
 * !! Keep your API limits in mind !!
 * @coversDefaultClass ShiftpiMetascanApi\Service\HashLookup
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class HashLookupTest extends \PHPUnit_Framework_TestCase
{
    /** @var HashLookup */
    protected $service;

    /** @var ServiceManager */
    protected $sm;

    public function setUp()
    {
        $this->sm = new ServiceManager();
        $this->sm->setAllowOverride(true);
        $this->sm->setShareByDefault(false);
        $this->sm->setInvokableClass(Result::class, Result::class)
            ->setFactory('ShiftpiMetascanApi\Http\Adapter', CurlFactory::class)
            ->setFactory('ShiftpiMetascanApi\Http\HashLookupRequest', HashLookupRequestFactory::class)
            ->setFactory('ShiftpiMetascanApi\Http\ApiRequest', ApiRequestFactory::class)
            ->setFactory('config', function() {
                return [
                    'metascan' => [
                        'hash_url' => 'https://hashlookup.metascan-online.com/v2/hash',
                        'key' => APIKEY
                    ],
                ];
            });

        $this->service = (new HashLookupFactory())->createService($this->sm);
    }

    /**
     * @covers ::lookup
     * @throws RequestFailedException
     */
    public function testNotFound()
    {
        $result = $this->service->lookup(md5('Some not infected and completely useless text ' . mt_rand()));

        $this->assertNull($result->getFileTypeCategory());
        $this->assertEquals(Result::RESULT_NOTFOUND, $result->getResult());
        $this->assertEquals(0, $result->getPercCompleted());
        $this->assertInternalType('array', $result->getRawResult());
        $this->assertNull($result->getTotalAvs());
        $this->assertNull($result->getFileId());
    }

    /**
     * @covers ::lookup
     * @throws RequestFailedException
     */
    public function testInfected()
    {
        $result = $this->service->lookup(md5('X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*'));

        $this->assertEquals(Result::FILETYPE_TEXT, $result->getFileTypeCategory());
        $this->assertEquals(Result::RESULT_INFECTED, $result->getResult());
        $this->assertEquals(100, $result->getPercCompleted());
        $this->assertInternalType('array', $result->getRawResult());
        $this->assertInternalType('int', $result->getTotalAvs());
    }

    /**
     * @covers ::lookup
     * @throws RequestFailedException
     */
    public function testClean()
    {
        $result = $this->service->lookup(md5(''));

        $this->assertEquals(Result::FILETYPE_OTHER, $result->getFileTypeCategory());
        $this->assertEquals(Result::RESULT_CLEAN, $result->getResult());
        $this->assertEquals(100, $result->getPercCompleted());
        $this->assertInternalType('array', $result->getRawResult());
        $this->assertInternalType('int', $result->getTotalAvs());
    }

    /**
     * @covers ::lookup
     */
    public function testWrongApiKey()
    {
        $config = $this->sm->get('config');
        $config['metascan']['key'] = '';

        $this->sm->setFactory('config', function() use ($config) {
            return $config;
        });
        $this->service = (new HashLookupFactory())->createService($this->sm);

        try {
            $this->service->lookup(md5(''));
        } catch (RequestFailedException $e) {
            $this->assertEquals(RequestFailedException::REASON_INVALIDAPIKEY, $e->getReason(true));
            return;
        }

        $this->fail('Exception has not been thrown');
    }

    /**
     * @covers ::lookup
     * @expectedException \InvalidArgumentException
     * @throws RequestFailedException
     */
    public function testInvalidHash()
    {
        $this->service->lookup(crc32('foo'));
    }
}