<?php

namespace NotaTools\Exception\HLBlock;

use Exception;
use Throwable;

/**
 * Class HLBlockException
 * @package NotaTools\Exception\HLBlock
 */
class HLBlockException extends Exception
{
    /**
     * IblockNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'ошибка HlBlock', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
