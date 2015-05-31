<?php
namespace ShiftpiMetascanApi\Service;

use Zend\Http\Response;

/**
 * Exception if an API request failed
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class RequestFailedException extends \Exception
{
    const REASON_BADREQUEST = Response::STATUS_CODE_400;
    const REASON_INVALIDAPIKEY = Response::STATUS_CODE_401;
    const REASON_LIMITREACHED = Response::STATUS_CODE_403;
    const REASON_INTERNALERROR = Response::STATUS_CODE_500;
    const REASON_SERVERBUSY = Response::STATUS_CODE_503;
    const REASON_NOTFOUND = -1;
    const REASON_UNKNOWN = 0;

    /** @var array */
    protected $reasons = [
        self::REASON_BADREQUEST => 'Bad request',
        self::REASON_INVALIDAPIKEY => 'Invalid API key',
        self::REASON_LIMITREACHED => 'API limit reached',
        self::REASON_INTERNALERROR => 'Internal server error',
        self::REASON_SERVERBUSY => 'Server is too busy',
        self::REASON_NOTFOUND => 'Scan not found',
        self::REASON_UNKNOWN => 'Unknown',
    ];

    /** @var int */
    protected $reason;

    /** @var string */
    protected $message = 'Request failed (Reason: %s "%s")';

    /**
     * Constructor
     * @param int $reason
     */
    public function __construct($reason)
    {
        $this->setReason($reason);
    }

    /**
     * @return int|string
     */
    public function getReason($key = false)
    {
        if ($key) {
            return $this->reason;
        }

        return $this->reasons[$this->reason];
    }

    /**
     * @param int $reason
     */
    public function setReason($reason)
    {
        if (!array_key_exists($reason, $this->reasons)) {
            $reason = static::REASON_UNKNOWN;
        }

        $this->reason = (int) $reason;
        $this->message = sprintf($this->message, $this->getReason(true), $this->getReason());
    }
}