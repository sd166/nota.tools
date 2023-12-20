<?php

namespace NotaTools\Exception\HLBlock;

use Throwable;

/**
 * Class HLBlockMoreThanOneException
 * @package NotaTools\Exception\HLBlock
 */
class HLBlockMoreThanOneException extends HLBlockException
{
    /**
     * IblockNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'HLBlock не найден', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
