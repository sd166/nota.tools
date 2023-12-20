<?php

namespace NotaTools\Agents;

use Exception;
use NotaTools\Helpers\LoggerHelper;

/**
 * Class AbstractAgents
 * @package NotaTools\Agents
 */
abstract class AbstractAgents
{
    /**
     * @return string
     */
    public static function getName(): string
    {
        return '\\'.static::class.'::execute();';
    }

    /**
     * @return string
     */
    public static function execute(): string
    {
        try {
            static::exec();
        } catch (Exception $e) {
            LoggerHelper::logAgent($e->getMessage(), static::class);
        }
        return static::getName();
    }

    /**
     * @return void
     */
    abstract public static function exec():void ;
}