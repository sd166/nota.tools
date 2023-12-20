<?php

namespace NotaTools\Exception\HLBlock;

use Throwable;

/**
 * Class HLBlockFieldNotFoundException
 * @package NotaTools\Exception\HLBlock
 */
class HLBlockFieldNotFoundException extends HLBlockException
{
    /**
     * IblockNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'поле HLBlock не найдено', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
