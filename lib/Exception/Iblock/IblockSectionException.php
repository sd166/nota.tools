<?php

namespace NotaTools\Exception\Iblock;

use Exception;
use Throwable;

/**
 * Class IblockSectionException
 * @package NotaTools\Exception\Iblock
 */
class IblockSectionException extends Exception
{
    /**
     * SectionNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'проблема с разделом', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
