<?php

namespace NotaTools\Exception\Rest;

use Bitrix\Rest\RestException;
use CRestServer;
use Exception;

/**
 * Class RestAccessException
 * @package NotaTools\Exception\Rest
 */
class RestAccessException extends RestException
{
    /**
     * RestFatalException constructor.
     *
     * @param                 $message
     * @param string          $code
     * @param string          $status
     * @param Exception|null  $previous
     */
    public function __construct(
        $message = 'доступ запрещен',
        $code = 'FORBIDDEN',
        $status = CRestServer::STATUS_FORBIDDEN,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $status, $previous);
    }
}