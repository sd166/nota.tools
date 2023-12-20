<?php

namespace NotaTools\Exception\Rest;

use CRestServer;
use Exception;

/**
 * Class RestFatalSaveException
 * @package NotaTools\Exception\Rest
 */
class RestFatalSaveException extends RestFatalException
{
    /**
     * RestFatalSaveException constructor.
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
        parent::__construct('Ошибка сохранения - ' . $message, $code, $status, $previous);
    }
}