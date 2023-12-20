<?php

namespace NotaTools\Exception\Rest;

use Bitrix\Rest\RestException;
use CRestServer;
use Exception;

/**
 * Class RestActionException
 * @package NotaTools\Exception\Rest
 */
class RestActionException extends RestException
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
        $message,
        $code = 'ACTION_EXCEPTION',
        $status = CRestServer::STATUS_OK,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $status, $previous);
    }
}