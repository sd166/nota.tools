<?php

namespace NotaTools\Exception\File;

use Exception;
use Throwable;

/**
 * Class FileException
 * @package NotaTools\Exception\File
 */
class FileException extends Exception
{
    /** @inheritDoc */
    public function __construct($message = 'Ошибка сохранения/получения файла', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}