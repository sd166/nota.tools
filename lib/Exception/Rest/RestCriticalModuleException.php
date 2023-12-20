<?php

namespace NotaTools\Exception\Rest;

use CRestServer;
use Exception;

/**
 * Class RestCriticalModuleException
 * @package NotaTools\Exception\Rest
 */
class RestCriticalModuleException extends RestCriticalException
{
    /**
     * RestCriticalModuleException constructor.
     *
     * @param                 $message
     * @param string          $code
     * @param string          $status
     * @param Exception|null  $previous
     */
    public function __construct(
        $message,
        $code = 'CRITICAL_ERROR',
        $status = CRestServer::STATUS_INTERNAL,
        Exception $previous = null
    ) {
        parent::__construct('Модуль не установлен - ' . $message, $code, $status, $previous);
    }
}