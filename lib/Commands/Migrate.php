<?php
/**
 * Bitrix Framework
 * @package    bitrix
 * @subpackage main
 * @copyright  2001-2018 Bitrix
 */

namespace NotaTools\Commands;

use Bitrix\Main\Loader;
use CModule;
use Exception;
use RuntimeException;
use Sprint\Migration\Console;
use Sprint\Migration\Module;
use sprint_migration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class Migrate
 * @package NotaTools\Commands
 */
class Migrate extends Command
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        $argv = $_SERVER['argv'];
        unset($argv[1]);
        ksort($argv);
        try {
            if (!Loader::includeModule('sprint.migration')) {
                /** @var sprint_migration $ob */
                if ($ob = CModule::CreateModuleObject('sprint.migration')) {
                    $ob->DoInstall();
                }
            }
            if (!Loader::includeModule('sprint.migration')) {
                Throw new RuntimeException('need to install module sprint.migration');
            }
            Module::checkHealth();
            $console = new Console($argv);
            $console->executeConsoleCommand();
        } catch (Exception|Throwable $e) {
            $output->writeln(sprintf("[%s] %s (%s)\n%s\n", get_class($e), $e->getMessage(), $e->getCode(), $e->getTraceAsString()));
        }
    }

    protected function configure()
    {
        $this->setName('migrate')->setDescription('migrate by sprint.migration')->setHelp('migrate by sprint.migration');
    }
}