<?php

namespace NotaTools\Exception\File;

use Throwable;

/**
 * Class FileNotFoundException
 * @package NotaTools\Exception\File
 */
class FileNotFoundException extends FileException
{
    /** @inheritDoc */
    public function __construct($message = 'файл не найден', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
