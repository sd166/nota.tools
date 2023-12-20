<?php

namespace NotaTools\Exception\UserField;

use Exception;
use Throwable;

/**
 * Class UserFieldNotFoundException
 * @package NotaTools\Exception\UserField
 */
class UserFieldNotFoundException extends Exception
{
    /**
     * IblockPropertyNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = 'пользовательское свойство не найдено',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
