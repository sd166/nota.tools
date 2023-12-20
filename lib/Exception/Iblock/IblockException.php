<?php

namespace NotaTools\Exception\Iblock;

use Exception;
use Throwable;

/**
 * Class IblockException
 * @package NotaTools\Exception\Iblock
 */
class IblockException extends Exception
{
    /**
     * IblockNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'ошибка инфоблока', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
