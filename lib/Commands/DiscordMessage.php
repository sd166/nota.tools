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
use GuzzleHttp\Exception\GuzzleException;
use NotaTools\Helpers\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DiscordMessage
 * @package NotaTools\Commands
 */
class DiscordMessage extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('discord:send_message')
            ->setDescription('set message to discord')
            ->setHelp('set message to discord')
            ->setDefinition(new InputDefinition([
                new InputArgument('web_hook_id', InputArgument::REQUIRED, 'web_hook_id'),
                new InputArgument('token', InputArgument::REQUIRED, 'token'),
                new InputArgument('username', InputArgument::REQUIRED, 'username'),
                new InputArgument('message', InputArgument::REQUIRED, 'message'),
            ]));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Loc::loadMessages(__FILE__);
        $webHookId = $input->getArgument('web_hook_id');
        $token = $input->getArgument('token');
        $userName = $input->getArgument('username');
        $message = $input->getArgument('message');
        $messageLog = Loc::getMessage($message);
        if (!empty($messageLog)) {
            $message = $messageLog;
        }
        $this->sendDiscordMessage($output, $webHookId, $token, $userName, $message);
    }

    /**
     * @param $output
     * @param $webHookId
     * @param $token
     * @param $userName
     * @param $message
     *
     * @throws GuzzleException
     */
    protected function sendDiscordMessage($output, $webHookId, $token, $userName, $message): void
    {
        $client = new Client();
        $url = 'https://discordapp.com/api/webhooks/' . $webHookId . '/' . $token;
        try{
            $response = $client->request('POST', $url, ['json' => ['username' => $userName, 'content' => $message]]);
            if ($response->getStatusCode() >= 300 || $response->getStatusCode() < 200) {
                $output->writeln('Ошибка добавления сообщения');
            }
        } catch (Exception $e){
            $output->writeln('Ошибка добавления сообщения');
            LoggerHelper::logCommand($e->getMessage(), __METHOD__);
        }
    }
}