<?php

namespace NotaTools\Exception\Iblock;

use Exception;
use Throwable;

/**
 * Class IblockPropertyException
 * @package NotaTools\Exception\Iblock
 */
class IblockPropertyException extends Exception
{
    /**
     * IblockFieldSettingsException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'проблема со свойствами', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
