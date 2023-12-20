<?php

namespace NotaTools\Helpers;

use Exception;
use NotaTools\Log\LoggerFactory;

/**
 * Class LoggerHelper
 * @package NotaTools\Helpers
 */
class LoggerHelper
{
    /**
     * @param string $message
     * @param string $logName
     * @param string $logType
     */
    public static function log(string $message, string $logName, string $logType = 'main'): void
    {
        try {
            $logger = LoggerFactory::create($logName, $logType);
            $logger->error($message);
        } catch (Exception $e) {
        }
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logAgent(string $message, string $logName): void
    {
        static::log($message, $logName, 'agents');
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logEvents(string $message, string $logName): void
    {
        static::log($message, $logName, 'events');
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logComponent(string $message, string $logName): void
    {
        static::log($message, $logName, 'component');
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logCommand(string $message, string $logName): void
    {
        static::log($message, $logName, 'command');
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logOrm(string $message, string $logName): void
    {
        static::log($message, $logName, 'orm');
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logService(string $message, string $logName): void
    {
        static::log($message, $logName, 'service');
    }

    /**
     * @param string $message
     * @param string $logName
     */
    public static function logTrait(string $message, string $logName): void
    {
        static::log($message, $logName, 'trait');
    }
}