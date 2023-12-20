<?php
/**
 * Bitrix Framework
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2018 Bitrix
 */

namespace NotaTools\Commands;

use Bitrix\Main\Localization\Loc;
use Exception;
use GuzzleHttp\Client;
use NotaTools\Helpers\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TelegramBot\Api\BotApi;

/**
 * Class TelegramMessage
 * @package NotaTools\Commands
 */
class TelegramMessage extends Command
{
    /**
     * * @return void
     */
    protected function configure()
    {
        $this->setName('telegram:send_message')
            ->setDescription('set message to telegram')
            ->setHelp('set message to telegram')
            ->setDefinition(new InputDefinition([
                new InputArgument('api', InputArgument::REQUIRED, 'api'),
                new InputArgument('chanel_id', InputArgument::REQUIRED, 'chanel_id'),
                new InputArgument('message', InputArgument::REQUIRED, 'message'),
                new InputArgument('proxy', InputArgument::OPTIONAL, 'proxy', ''),
            ]));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Loc::loadMessages(__FILE__);
        $api = $input->getArgument('api');
        $chatId = $input->getArgument('chanel_id');
        $chatId = str_replace('z0z0z0', '', $chatId);
        $message = $input->getArgument('message');
        $proxy = $input->getArgument('proxy');
        $messageLog = Loc::getMessage($message);
        if (!empty($messageLog)) {
            $message = $messageLog;
        }
        $this->sendWithComposerPackage($output, $api, $chatId, $message, $proxy);
    }

    /**
     * @param        $output
     * @param        $api
     * @param        $chatId
     * @param        $message
     * @param string $proxy
     */
    protected function sendWithComposerPackage($output, $api, $chatId, $message, $proxy = 'socks5://104.248.63.18:30588'): void
    {
        try {
            $bot = new BotApi($api);
            if(!empty($proxy)){
                //если отвалится - меняем proxy
                $bot->setProxy($proxy);
            }
            $bot->sendMessage($chatId, $message);
        } catch (Exception $e) {
            $output->writeln('Ошибка добавления сообщения');
            LoggerHelper::logCommand($e->getMessage(), __METHOD__);
        }
    }
}