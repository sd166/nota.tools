<?php

namespace NotaTools\Exception\Orm\Model;

use Exception;
use Throwable;

/**
 * Class ModelException
 * @package NotaTools\Exception\Orm\Model
 */
class ModelException extends Exception
{
    /**
     * ModelException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Ошибка модели', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
