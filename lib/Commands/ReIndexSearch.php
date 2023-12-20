<?php
/**
 * Bitrix Framework
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2018 Bitrix
 */

namespace NotaTools\Commands;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyIndex\Manager;
use Bitrix\Iblock\SectionPropertyTable;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use NotaTools\BitrixUtils;
use CSearch;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReIndexSearch
 * @package NotaTools\Commands
 */
class ReIndexSearch extends AbstractCommand
{
    public const BEGIN_MESSAGE = 'Начало переиндексации';
    public const END_MESSAGE = 'Переиндексация завершена';

    protected function configure()
    {
        $this->setName('search:reindex')
            ->setAliases(['s:r'])
            ->setDescription('Search reindex')
            ->setHelp('Search reindex')
            ->setDefinition(new InputDefinition([
                new InputOption('full', 'f', InputOption::VALUE_OPTIONAL, 'module to reindex', 'Y'),
                new InputOption('module', 'm', InputOption::VALUE_OPTIONAL, 'module to reindex', 'all'),
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $output->writeln('');
        $module = $input->getOption('module');
        $full = $input->getOption('full') === BitrixUtils::BX_BOOL_TRUE;
        if ($module === 'all') {
            $NS = false;
            $NS = CSearch::ReIndexAll($full, 0, $NS);
            while(is_array($NS)) {
                $output->writeln('Проидексирвоано '.$NS['CNT']);
                $NS = CSearch::ReIndexAll($full, 0, $NS);
            }
            $count = $NS;
        } else {
            $res = \CSearch::ReindexModule($module, $full);
            $count = $res['CNT'];
        }
        $output->writeln('Проиндексировано: ' . $count . ' элементов');
        $output->writeln('');
    }

}