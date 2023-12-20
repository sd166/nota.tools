<?php

namespace NotaTools\Exception\Rest;

use Bitrix\Rest\RestException;
use CRestServer;
use Exception;

/**
 * Class RestNotFoundException
 * @package NotaTools\Exception\Rest
 */
class RestNotFoundException extends RestException
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
        $code = 'NOT_FOUND',
        $status = CRestServer::STATUS_NOT_FOUND,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $status, $previous);
    }
}