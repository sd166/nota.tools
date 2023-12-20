<?php
/**
 * Bitrix Framework
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2018 Bitrix
 */

namespace NotaTools\Commands;

use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 * @package NotaTools\Commands
 */
abstract class AbstractCommand extends Command
{
    public const BEGIN_MESSAGE = '';
    public const END_MESSAGE = '';

    protected function configure()
    {
        Loader::setRequireThrowException(false);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws SystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            static::BEGIN_MESSAGE,
            '==============',
        ]);
        $time = getmicrotime();
        $memoryBefore = memory_get_usage();
        $this->exec($input, $output);
        // summary stats
        $time = round(getmicrotime() - $time, 2);
        $memoryAfter = memory_get_usage();
        $memoryDiff = $memoryAfter - $memoryBefore;
        $output->writeln([
            static::END_MESSAGE,
            '==============',
        ]);
        $output->writeln('Time: ' . $time . ' sec');
        $output->writeln('Memory usage: ' . (round($memoryAfter / 1024 / 1024, 1)) . 'M (+' . (round($memoryDiff / 1024 / 1024, 1)) . 'M)');
        $output->writeln('Memory peak usage: ' . (round(memory_get_peak_usage() / 1024 / 1024, 1)) . 'M');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    abstract protected function exec(InputInterface $input, OutputInterface $output): void;
}