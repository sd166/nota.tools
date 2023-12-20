<?php

namespace NotaTools\Exception\User;

use Exception;
use Throwable;

/**
 * Class UserNotFoundException
 * @package NotaTools\Exception\User
 */
class UserNotFoundException extends Exception
{
    /**
     * UserNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Пользователь не найден', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}