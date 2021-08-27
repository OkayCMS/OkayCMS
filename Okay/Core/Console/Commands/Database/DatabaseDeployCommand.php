<?php

namespace Okay\Core\Console\Commands\Database;

use Aura\Sql\ExtendedPdo;
use Okay\Core\Config;
use Okay\Core\Console\Command;
use Okay\Core\DataCleaner;
use Okay\Core\ServiceLocator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class DatabaseDeployCommand extends Command
{
    protected static $defaultName = 'database:deploy';

    protected static $defaultDescription = 'Deploying a clean database.';

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceLocator = ServiceLocator::getInstance();

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        /** @var Config $config */
        $config = $serviceLocator->getService(Config::class);

        $output->writeln("\n***************************");

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Deploy clean database? ', false))) {
            return Command::FAILURE;
        }

        $filename = $input->getOption('file_path');

        if (!file_exists($filename)) {
            $output->writeln("Database file does not exist ({$filename})");
            return Command::FAILURE;
        }

        if ($questionHelper->ask($input, $output, new ConfirmationQuestion('Set new credentials for database? ', false))) {
            $server = $questionHelper->ask($input, $output, new Question('Enter database SERVER: ', false));
            $user = $questionHelper->ask($input, $output, new Question('Enter database USER: ', false));
            $password = $questionHelper->ask($input, $output, new Question('Enter database PASSWORD: ', false));
            $name = $questionHelper->ask($input, $output, new Question('Enter database NAME: ', false));
            $driver = $config->get('db_driver');
            $charset = $config->get('db_charset');

            $pdo = new ExtendedPdo("{$driver}:host={$server};dbname={$name};charset={$charset}", $user, $password);
            $pdo->connect();

            $config->set('db_server', $server);
            $config->set('db_user', $user);
            $config->set('db_password', $password);
            $config->set('db_name', $name);
        } else {
            $server = $config->get('db_server');
            $user = $config->get('db_user');
            $password = $config->get('db_password');
            $name = $config->get('db_name');
            $driver = $config->get('db_driver');
            $charset = $config->get('db_charset');

            $pdo = new ExtendedPdo("{$driver}:host={$server};dbname={$name};charset={$charset}", $user, $password);
            $pdo->connect();
        }

        $this->restore($pdo, $filename);

        if ($questionHelper->ask($input, $output, new ConfirmationQuestion('Delete demo content? ', false))) {
            /** @var DataCleaner $dataCleaner */
            $dataCleaner = $serviceLocator->getService(DataCleaner::class);

            $dataCleaner->clearAllCatalogImages();
            $dataCleaner->clearCatalogData();
        }

        $output->writeln("\nDatabase deployed successfully!");
        $output->writeln("***************************");


        return Command::SUCCESS;
    }

    private function restore($pdo, $filename)
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

    private function isComment($line)
    {
        return substr($line, 0, 2) == '--';
    }

    private function isQueryEnd($line)
    {
        return substr(trim($line), -1, 1) == ';';
    }
}