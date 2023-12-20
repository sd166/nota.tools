<?php

namespace NotaTools\Exception\File;

use Throwable;

/**
 * Class FileSaveException
 * @package NotaTools\Exception\File
 */
class FileSaveException extends FileException
{
    /** @inheritDoc */
    public function __construct($message = 'ошибка при сохранении файла', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
