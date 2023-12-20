<?php

namespace NotaTools\Exception\File;

use Throwable;

/**
 * Class FileMimeTypeException
 * @package NotaTools\Exception\File
 */
class FileMimeTypeException extends FileException
{
    /** @inheritDoc */
    public function __construct($message = 'неверный тип файла', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
