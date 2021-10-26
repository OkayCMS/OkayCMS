<?php

namespace Okay\Core\Console\Commands\Database;

use Aura\Sql\ExtendedPdo;
use Okay\Core\Config;
use Okay\Core\Console\Command;
use Okay\Core\DataCleaner;
use Symfony\Component\Console\Input\InputOption;

class DatabaseDeployCommand extends Command
{
    protected static $defaultName = 'database:deploy';
    protected static $defaultDescription = 'Deploys a clean database.';

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to set database credentials and execute clean deploy.')
            ->addOption(
                'file_path',
                null,
                InputOption::VALUE_REQUIRED,
                'The database file path',
                dirname(__DIR__, 5).'/1DB_changes/okay_clean.sql'
            );
    }

    protected function handle(Config $config): int
    {
        $this->output->writeln("\n***************************");

        if (!$this->askConfirmation('Deploy clean database? ', false)) {
            return Command::FAILURE;
        }

        $filename = $this->input->getOption('file_path');

        if (!file_exists($filename)) {
            $this->output->writeln("Database file does not exist ({$filename})");
            return Command::FAILURE;
        }

        $server = $config->get('db_server');
        $user = $config->get('db_user');
        $password = $config->get('db_password');
        $name = $config->get('db_name');
        $driver = $config->get('db_driver');
        $charset = $config->get('db_charset');

        if ($this->askConfirmation('Set new credentials for database? ', false)) {
            $server = $this->ask("Enter database SERVER({$server}): ", $server);
            $user = $this->ask("Enter database USER({$user}): ", $user);
            $password = $this->ask("Enter database PASSWORD({$password}): ", $password);
            $name = $this->ask("Enter database NAME({$name}): ", $name);

            $pdo = new ExtendedPdo("{$driver}:host={$server};dbname={$name};charset={$charset}", $user, $password);
            $pdo->connect();

            $config->set('db_server', $server);
            $config->set('db_user', $user);
            $config->set('db_password', "\"{$password}\"");
            $config->set('db_name', $name);
        } else {
            $pdo = new ExtendedPdo("{$driver}:host={$server};dbname={$name};charset={$charset}", $user, $password);
            $pdo->connect();
        }

        $this->restore($pdo, $filename);

        if ($this->askConfirmation('Delete demo content? ', false)) {
            /** @var DataCleaner $dataCleaner */
            $dataCleaner = $this->serviceLocator->getService(DataCleaner::class);

            $dataCleaner->clearAllCatalogImages();
            $dataCleaner->clearCatalogData();
        }

        $this->output->writeln("\nDatabase deployed successfully!");
        $this->output->writeln("***************************");


        return Command::SUCCESS;
    }

    private function restore(ExtendedPdo $pdo, string $filename): void
    {
        $migration = fopen($filename, 'r');
        if(empty($migration)) {
            return;
        }

        $migrationQuery = '';
        while(!feof($migration)) {
            $line = fgets($migration);
            if ($this->isComment($line) || empty($line)) {
                continue;
            }

            $migrationQuery .= $line;
            if (!$this->isQueryEnd($line)) {
                continue;
            }

            try {
                $pdo->perform($migrationQuery);
            } catch(\PDOException $e) {
                print 'Error performing query \'<b>'.$migrationQuery.'</b>\': '.$e->getMessage().'<br/><br/>';
            }

            $migrationQuery = '';
        }

        fclose($migration);
    }

    private function isComment(string $line): string
    {
        return substr($line, 0, 2) == '--';
    }

    private function isQueryEnd(string $line): string
    {
        return substr(trim($line), -1, 1) == ';';
    }
}