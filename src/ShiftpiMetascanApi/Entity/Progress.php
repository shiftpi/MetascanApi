<?php
namespace ShiftpiMetascanApi\Entity;

/**
 * Current api progress
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class Progress
{
    /** @var string */
    protected $dataId;

    /** @var string */
    protected $restIp;

    /**
     * @return string
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     * @param string $dataId
     */
    public function setDataId($dataId)
    {
        $this->dataId = $dataId;
    }

    /**
     * @return string
     */
    public function getRestIp()
    {
        return $this->restIp;
    }

    /**
     * @param string $restIp
     */
    public function setRestIp($restIp)
    {
        $this->restIp = $restIp;
    }
}