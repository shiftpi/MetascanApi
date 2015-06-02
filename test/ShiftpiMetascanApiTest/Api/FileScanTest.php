<?php
namespace ShiftpiMetascanApiTest\Api;

use ShiftpiMetascanApi\Entity\Result;
use ShiftpiMetascanApi\Entity\Progress;
use ShiftpiMetascanApi\Http\ApiRequestFactory;
use ShiftpiMetascanApi\Http\ScanRequestFactory;
use ShiftpiMetascanApi\Service\RequestFailedException;
use ShiftpiMetascanApi\Service\Scan;
use ShiftpiMetascanApi\Service\ScanFactory;
use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests the Scan class against the API
 * !! Keep your API limits in mind !!
 * @coversDefaulClass ShiftpiMetascanApi\Service\Scan
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class FileScanTest extends \PHPUnit_Framework_TestCase
{
    /** @var Scan */
    protected $service;

    /** @var ServiceManager */
    protected $sm;

    public function setUp()
    {
        $this->sm = new ServiceManager();
        $this->sm->setAllowOverride(true);
        $this->sm->setShareByDefault(false);
        $this->sm->setInvokableClass(Result::class, Result::class)
            ->setInvokableClass(Progress::class, Progress::class)
            ->setFactory('ShiftpiMetascanApi\Http\ScanRequest', ScanRequestFactory::class)
            ->setFactory('ShiftpiMetascanApi\Http\ApiRequest', ApiRequestFactory::class)
            ->setFactory('config', function() {
                return [
                    'metascan' => [
                        'data_url' => 'https://scan.metascan-online.com/v2/file',
                        'key' => APIKEY
                    ],
                ];
            });

        $this->service = (new ScanFactory())->createService($this->sm);
    }

    /**
     * @covers ::scan
     * @throws RequestFailedException
     */
    public function testClean()
    {
        $data = 'Some not infected and completely useless text ' . mt_rand();
        $filename = 'file_' . mt_rand() . '.txt';

        $result = $this->service->scan($data, $filename);

        $this->assertEquals(Result::FILETYPE_TEXT, $result->getFileTypeCategory());
        $this->assertEquals(Result::RESULT_CLEAN, $result->getResult());
        $this->assertEquals(100, $result->getPercCompleted());
        $this->assertInternalType('array', $result->getRawResult());
        $this->assertInternalType('int', $result->getTotalAvs());
    }

    /**
     * @covers ::scan
     * @throws RequestFailedException
     */
    public function testEncrypted()
    {
        $password = 'secret';
        $cipher = BlockCipher::factory('mcrypt', ['algo' => 'aes']);
        $cipher->setKey($password);
        $data = $cipher->encrypt('Some not infected and completely useless but compressed text '
            . mt_rand());
        $filename = 'file_' . mt_rand() . '.txt';

        $result = $this->service->scan($data, $filename, $password);

        $this->assertEquals(Result::FILETYPE_TEXT, $result->getFileTypeCategory());
        $this->assertEquals(Result::RESULT_CLEAN, $result->getResult());
        $this->assertEquals(100, $result->getPercCompleted());
        $this->assertInternalType('array', $result->getRawResult());
        $this->assertInternalType('int', $result->getTotalAvs());
    }

    /**
     * @covers ::scan
     * @throws RequestFailedException
     */
    public function testInfected()
    {
        $data = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';
        $filename = 'file_' . mt_rand() . '.txt';

        $result = $this->service->scan($data, $filename);

        $this->assertEquals(Result::FILETYPE_TEXT, $result->getFileTypeCategory());
        $this->assertEquals(Result::RESULT_INFECTED, $result->getResult());
        $this->assertEquals(100, $result->getPercCompleted());
        $this->assertInternalType('array', $result->getRawResult());
        $this->assertInternalType('int', $result->getTotalAvs());
    }

    /**
     * @covers ::scan
     */
    public function testWrongUrl()
    {
        $config = $this->sm->get('config');
        $config['metascan']['key'] = '';

        $this->sm->setFactory('config', function() use ($config) {
            return $config;
        });
        $this->service = (new ScanFactory())->createService($this->sm);

        try {
            $this->service->scan('');
        } catch (RequestFailedException $e) {
            $this->assertEquals(RequestFailedException::REASON_INVALIDAPIKEY, $e->getReason(true));
            return;
        }

        $this->fail('Exception has not been thrown');
    }
}