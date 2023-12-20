<?php

namespace NotaTools\Exception\Iblock;

use Throwable;

/**
 * Class IblockFieldSettingsException
 * @package NotaTools\Exception\Iblock
 */
class IblockFieldSettingsException extends IblockException
{
    /**
     * IblockFieldSettingsException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Не найдены поля инфоблока', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
