<?php

namespace NotaTools\Exception\Rest;

use Bitrix\Rest\RestException;
use CRestServer;
use Exception;

/**
 * Class RestCriticalException
 * @package NotaTools\Exception\Rest
 */
class RestCriticalException extends RestException
{
    /**
     * RestCriticalException constructor.
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
        parent::__construct($message, $code, $status, $previous);
    }
}