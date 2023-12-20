<?php

namespace NotaTools\Exception\Rest;

use CRestServer;
use Exception;

/**
 * Class RestFatalIblockException
 * @package NotaTools\Exception\Rest
 */
class RestFatalIblockException extends RestFatalException
{
    /**
     * RestFatalIblockException constructor.
     *
     * @param                 $message
     * @param string          $code
     * @param string          $status
     * @param Exception|null  $previous
     */
    public function __construct(
        $message,
        $code = 'FATAL_ERROR',
        $status = CRestServer::STATUS_INTERNAL,
        Exception $previous = null
    ) {
        parent::__construct('Инфоблок не найден - ' . $message, $code, $status, $previous);
    }
}