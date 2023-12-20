<?php

namespace NotaTools\Exception\Rest;

use Bitrix\Rest\RestException;
use CRestServer;
use Exception;

/**
 * Class RestValidateException
 * @package NotaTools\Exception\Rest
 */
class RestValidateException extends RestException
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
        $code = 'VALIDATE_EXCEPTION',
        $status = CRestServer::STATUS_WRONG_REQUEST,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $status, $previous);
    }
}