<?php

namespace NotaTools\Exception\UserField;

use Exception;
use Throwable;

/**
 * Class UserFieldEnumNotFoundException
 * @package NotaTools\Exception\UserField
 */
class UserFieldEnumNotFoundException extends Exception
{
    /**
     * PropertyEnumNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = 'Значение пользовательского свойства типа список не найдено',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
