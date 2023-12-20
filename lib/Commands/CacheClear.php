<?php
/**
 * Bitrix Framework
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2018 Bitrix
 */

namespace NotaTools\Commands;

use Bitrix\Main\Application;
use Bitrix\Main\SystemException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheClear
 * @package NotaTools\Commands
 */
class CacheClear extends AbstractCommand
{
    public const BEGIN_MESSAGE = 'Начата очистка кеша';
    public const END_MESSAGE = 'Очистка кеша завершена';

    protected function configure()
    {
        $this->setName('cache:clear')
            ->setAliases(['c:c'])
            ->setDescription('Clear cache')
            ->setHelp('Clear cache')
            ->setDefinition(new InputDefinition([
                new InputOption('type', 't', InputOption::VALUE_OPTIONAL, 'clear cache type', 'full'),
            ]));
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     * @throws SystemException
     */
    protected function exec(InputInterface $input, OutputInterface $output): void
    {
        $type = $input->getOption('type');
        $instance = Application::getInstance();
        switch ($type) {
            case 'full':
            default:
                $instance->getTaggedCache()->clearByTag(true);
                $instance->getCache()->cleanDir();
                $instance->getManagedCache()->cleanAll();
                break;
        }
    }
}