<?php

namespace NotaTools\Exception\Iblock;

use Throwable;

/**
 * Class SectionNotFoundException
 * @package NotaTools\Exception\Iblock
 */
class SectionNotFoundException extends IblockSectionException
{
    /**
     * SectionNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Раздел не найден', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
