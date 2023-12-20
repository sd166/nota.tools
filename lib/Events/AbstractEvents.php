<?php

namespace NotaTools\Events;

use Bitrix\Main\EventManager;

/**
 * Class AbstractEvents
 * @package NotaTools\Events
 */
abstract class AbstractEvents
{
    /**
     * @var string
     */
    protected const BASE_MODULE_ID = 'nota.tools';

    public static function bind(): void
    {
        $eventManager = EventManager::getInstance();
        $events = static::getEvents();
        foreach ($events as $event) {
            $eventManager->registerEventHandler($event['FROM'], $event['EVENT'], $event['TO'] ?: static::BASE_MODULE_ID,
                $event['CLASS'] ?: static::class, $event['METHOD'], $event['SORT'] ?: 100, $event['PATH'] ?: '', $event['ARGS'] ?: []);
        }
    }

    public static function unBind(): void
    {
        $eventManager = EventManager::getInstance();
        $events = static::getEvents();
        foreach ($events as $event) {
            $eventManager->unRegisterEventHandler($event['FROM'], $event['EVENT'], $event['TO'] ?: static::BASE_MODULE_ID,
                $event['CLASS'] ?: static::class, $event['METHOD'], $event['PATH'] ?: '', $event['ARGS'] ?: []);
        }
    }

    public static function bindLocal(): void
    {
        $eventManager = EventManager::getInstance();
        $events = static::getEvents();
        foreach ($events as $event) {
            $eventManager->addEventHandler($event['FROM'], $event['EVENT'], !empty($event['FUNCTION']) ? $event['FUNCTION'] : [$event['CLASS'] ?: static::class, $event['METHOD']]);
        }
    }

    /**
     * @return array
     */
    abstract public static function getEvents(): array;
}