<?php

namespace NotaTools\Exception\HLBlock;

use Throwable;

/**
 * Class HLBlockNotFoundException
 * @package NotaTools\Exception\HLBlock
 */
class HLBlockNotFoundException extends HLBlockException
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
