<?php

namespace NotaTools\Exception\Iblock;

use Throwable;

/**
 * Class IblockNotFoundException
 * @package NotaTools\Exception\Iblock
 */
class IblockNotFoundException extends IblockException
{
    /**
     * IblockNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Инфоблок не найден', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
