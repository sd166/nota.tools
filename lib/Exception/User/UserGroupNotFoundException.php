<?php

namespace NotaTools\Exception\User;

use Exception;
use Throwable;

/**
 * Class UserGroupNotFoundException
 * @package NotaTools\Exception\User
 */
class UserGroupNotFoundException extends Exception
{
    /**
     * UserGroupNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Группа пользователя не найдена', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
