<?php

namespace NotaTools\Exception;

use Exception;
use Throwable;

/**
 * Class WrongPhoneNumberException
 * @package NotaTools\Exception
 */
class WrongPhoneNumberException extends Exception
{
    /**
     * WrongPhoneNumberException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Неверный номер телефона', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
