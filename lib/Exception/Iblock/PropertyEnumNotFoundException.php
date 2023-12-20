<?php

namespace NotaTools\Exception\Iblock;

use Throwable;

/**
 * Class PropertyEnumNotFoundException
 * @package NotaTools\Exception\Iblock
 */
class PropertyEnumNotFoundException extends IblockPropertyException
{
    /**
     * PropertyEnumNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = 'Значение свойства типа список не найдено',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
