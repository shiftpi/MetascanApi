<?php
namespace ShiftpiMetascanApi\Entity;

/**
 * Scan report
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
class Result
{
    const RESULT_CLEAN = 0;
    const RESULT_INFECTED = 1;
    const RESULT_SUSPICIOUS = 2;
    const RESULT_FAILEDTOSCAN = 3;
    const RESULT_SKIPPEDCLEAN = 7;
    const RESULT_SKIPPEDDIRTY = 8;
    const RESULT_EXCEEDEDARCHIVEDEPTH = 9;
    const RESULT_NOTSCANNED = 10;
    const RESULT_ABORTED = 11;
    const RESULT_ENCRYPTED = 12;
    const RESULT_EXCEEDEDARCHIVESIZE = 13;
    const RESULT_EXCEEDEDARCHIVEFILENUMBER = 14;

    const FILETYPE_EXEC = 'E';
    const FILETYPE_DOC = 'D';
    const FILETYPE_ARCHIVE = 'A';
    const FILETYPE_GRAPHIC = 'G';
    const FILETYPE_TEXT = 'T';
    const FILETYPE_PDF = 'P';
    const FILETYPE_AUDVID = 'M';
    const FILETYPE_MAIL = 'Z';
    const FILETYPE_OTHER = 'O';

    /** @var string */
    protected $fileId;

    /** @var int */
    protected $result;

    /** @var string */
    protected $fileTypeCategory;

    /** @var int */
    protected $totalAvs;

    /** @var array */
    protected $rawResult;

    /** @var int */
    protected $percCompleted = 0;

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param string $fileId
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param int $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getFileTypeCategory()
    {
        return $this->fileTypeCategory;
    }

    /**
     * @param string $fileTypeCategory
     */
    public function setFileTypeCategory($fileTypeCategory)
    {
        $this->fileTypeCategory = $fileTypeCategory;
    }

    /**
     * @return int
     */
    public function getTotalAvs()
    {
        return $this->totalAvs;
    }

    /**
     * @param int $totalAvs
     */
    public function setTotalAvs($totalAvs)
    {
        $this->totalAvs = $totalAvs;
    }

    /**
     * @return array
     */
    public function getRawResult()
    {
        return $this->rawResult;
    }

    /**
     * @param array $rawResult
     */
    public function setRawResult($rawResult)
    {
        $this->rawResult = $rawResult;
    }

    /**
     * @return int
     */
    public function getPercCompleted()
    {
        return $this->percCompleted;
    }

    /**
     * @param int $percCompleted
     */
    public function setPercCompleted($percCompleted)
    {
        $this->percCompleted = (int) $percCompleted;
    }
}