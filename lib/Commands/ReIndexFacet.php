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
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReCreateFacet
 * @package NotaTools\Commands
 */
class ReIndexFacet extends AbstractCommand
{
    public const BEGIN_MESSAGE = 'Начало переиндексации';
    public const END_MESSAGE = 'Переиндексация завершена';

    protected function configure()
    {
        $this->setName('facet:reindex')
            ->setAliases(['f:r'])
            ->setDescription('Facet reindex')
            ->setHelp('Facet reindex')
            ->setDefinition(new InputDefinition([
                new InputOption('iblock-id', 'i', InputOption::VALUE_OPTIONAL, 'iblock to reindex', 'all'),
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
        $output->writeln('');
        $iblockId = $input->getOption('iblock-id');
        if ($iblockId === 'all') {
            $collection = SectionPropertyTable::query()->setSelect(['IBLOCK_ID'])->setGroup('IBLOCK_ID')->exec()->fetchAll();
            $iblockIds = array_unique(array_column($collection, 'IBLOCK_ID'));
        } else {
            $iblockIds = [(int)$iblockId];
        }
        foreach ($iblockIds as $iblockId) {
            $totalItems = (int)ElementTable::getCount((new ConditionTree())->where('IBLOCK_ID', $iblockId), ['ttl' => 1800]);
            $index = Manager::createIndexer($iblockId);
            $index->startIndex();
            $indexCount = (int)$index->continueIndex();
            if ($indexCount > 0) {
                $output->writeln('Проиндексировано: ' . $indexCount . ' из ' . $totalItems . ' элементов инфоблока ' . $iblockId);
                if ($indexCount === $totalItems) {
                    $index->endIndex();
                    $output->writeln([
                        'Переиндексация инфоблока ' . $iblockId . ' завершена',
                        '====',
                    ]);
                }
            } else {
                $index->endIndex();
                $output->writeln([
                    'Переиндексация инфоблока ' . $iblockId . ' завершена',
                    '====',
                ]);
            }
        }
        $output->writeln('');
    }

}