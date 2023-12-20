<?php

namespace NotaTools\Commands;

use Bitrix\Main\Cli\OrmAnnotateCommand;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class RegisterCommands
 * @package NotaTools\Commands
 */
class RegisterCommands
{
    /**
     * @param Event $event
     *
     * @return EventResult
     */
    public static function execute(Event $event): EventResult
    {
        $commands = new ArrayCollection();
        $commands->add(OrmAnnotateCommand::class);
        $commands->add(Migrate::class);
        $commands->add(CacheClear::class);
        $commands->add(ReIndexFacet::class);
        $commands->add(ReIndexSearch::class);
        $commands->add(DiscordMessage::class);
        $commands->add(TelegramMessage::class);
        return new EventResult($event->getEventType(), ['COMMANDS' => $commands]);
    }
}