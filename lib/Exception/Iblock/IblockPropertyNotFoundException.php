<?php

namespace NotaTools\Exception\Iblock;

use Throwable;

/**
 * Class IblockPropertyNotFoundException
 * @package NotaTools\Exception\Iblock
 */
class IblockPropertyNotFoundException extends IblockPropertyException
{
    /**
     * IblockPropertyNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'свойство не найдено', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
