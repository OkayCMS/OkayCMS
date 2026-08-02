<?php

declare(strict_types=1);

namespace Okay\Core\Console\Commands\Database;

use Aura\Sql\ExtendedPdo;
use Okay\Core\Config;
use Okay\Core\Console\Command;
use Okay\Core\Upgrade\DatabaseUpdateFile;
use Okay\Core\Upgrade\DatabaseUpdateSelector;
use Okay\Core\Upgrade\SocialShareSettingsNormalizer;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

class DatabaseUpgradeCommand extends Command
{
    protected static string $defaultName = 'database:upgrade';
    protected static string $defaultDescription = 'Applies ordered release database update scripts.';

    private DatabaseUpdateSelector $selector;
    private SocialShareSettingsNormalizer $socialShareSettingsNormalizer;

    public function __construct(
        ?DatabaseUpdateSelector $selector = null,
        ?SocialShareSettingsNormalizer $socialShareSettingsNormalizer = null
    ) {
        parent::__construct();

        $this->selector = $selector ?? new DatabaseUpdateSelector();
        $this->socialShareSettingsNormalizer = $socialShareSettingsNormalizer ?? new SocialShareSettingsNormalizer();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Selects and optionally applies 1DB_changes/update_*.sql files between release versions.')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Current installed release version.')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Target release version.')
            ->addOption(
                'updates-dir',
                null,
                InputOption::VALUE_REQUIRED,
                'Directory with update_*.sql files.',
                dirname(__DIR__, 5) . '/1DB_changes'
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Print selected SQL files without executing them.')
            ->addOption('yes', 'y', InputOption::VALUE_NONE, 'Apply without interactive confirmation.');
    }

    protected function handle(): int
    {
        $fromVersion = (string) $this->input->getOption('from');
        $toVersion = (string) $this->input->getOption('to');
        $updatesDir = (string) $this->input->getOption('updates-dir');

        if ($fromVersion === '' || $toVersion === '') {
            $this->output->writeln('Both --from and --to options are required.');
            return Command::FAILURE;
        }

        try {
            $updates = $this->selector->select($updatesDir, $fromVersion, $toVersion);
        } catch (Throwable $exception) {
            $this->output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        $postSqlNormalizers = $this->selectPostSqlNormalizers($fromVersion, $toVersion);

        $this->writePlan($fromVersion, $toVersion, $updates, $postSqlNormalizers);

        if ($this->input->getOption('dry-run') || ($updates === [] && $postSqlNormalizers === [])) {
            return Command::SUCCESS;
        }

        if (!$this->input->getOption('yes') && !$this->askConfirmation('Apply selected database updates? ', false)) {
            return Command::FAILURE;
        }

        /** @var Config $config */
        $config = $this->serviceLocator->getService(Config::class);
        $pdo = new ExtendedPdo(
            "{$config->get('db_driver')}:host={$config->get('db_server')};dbname={$config->get('db_name')};"
                . "charset={$config->get('db_charset')}",
            $config->get('db_user'),
            $config->get('db_password')
        );

        foreach ($updates as $update) {
            if (!$this->applySqlFile($pdo, $update)) {
                return Command::FAILURE;
            }
        }

        foreach ($postSqlNormalizers as $postSqlNormalizer) {
            if ($postSqlNormalizer === SocialShareSettingsNormalizer::NAME) {
                $changed = $this->socialShareSettingsNormalizer->normalize($pdo);
                $this->output->writeln(
                    "Applied {$postSqlNormalizer} normalizer ({$changed} setting(s) changed)."
                );
            }
        }

        $this->output->writeln('Database upgrade scripts applied successfully.');

        return Command::SUCCESS;
    }

    /**
     * @param list<DatabaseUpdateFile> $updates
     * @param list<string> $postSqlNormalizers
     */
    private function writePlan(
        string $fromVersion,
        string $toVersion,
        array $updates,
        array $postSqlNormalizers
    ): void {
        $this->output->writeln("Database upgrade range: {$fromVersion} -> {$toVersion}");
        $this->output->writeln('Selected SQL files: ' . count($updates));

        foreach ($updates as $update) {
            $this->output->writeln('  - ' . $update->getPath());
        }

        $this->output->writeln('Post-SQL normalizers: ' . count($postSqlNormalizers));

        foreach ($postSqlNormalizers as $postSqlNormalizer) {
            $this->output->writeln('  - ' . $postSqlNormalizer);
        }
    }

    private function applySqlFile(ExtendedPdo $pdo, DatabaseUpdateFile $update): bool
    {
        $migration = fopen($update->getPath(), 'r');
        if ($migration === false) {
            $this->output->writeln("Unable to read SQL file: {$update->getPath()}");
            return false;
        }

        $query = '';
        while (($line = fgets($migration)) !== false) {
            if ($this->isComment($line) || trim($line) === '') {
                continue;
            }

            $query .= $line;
            if (!$this->isQueryEnd($line)) {
                continue;
            }

            try {
                $pdo->perform($query);
            } catch (Throwable $exception) {
                fclose($migration);
                $this->output->writeln("SQL update failed in {$update->getPath()}: {$exception->getMessage()}");
                return false;
            }

            $query = '';
        }

        fclose($migration);
        $this->output->writeln("Applied {$update->getPath()}");

        return true;
    }

    private function isComment(string $line): bool
    {
        return str_starts_with(trim($line), '--');
    }

    private function isQueryEnd(string $line): bool
    {
        return str_ends_with(trim($line), ';');
    }

    /**
     * @return list<string>
     */
    private function selectPostSqlNormalizers(string $fromVersion, string $toVersion): array
    {
        $fromVersion = $this->normalizeVersion($fromVersion);
        $toVersion = $this->normalizeVersion($toVersion);

        if (
            version_compare($fromVersion, '4.6.0', '<')
            && version_compare($toVersion, '4.6.0', '>=')
        ) {
            return [SocialShareSettingsNormalizer::NAME];
        }

        return [];
    }

    private function normalizeVersion(string $version): string
    {
        $version = preg_replace('/^v(?=\d)/', '', trim($version)) ?? $version;
        $version = str_replace('_', '-', $version);

        return strtolower($version);
    }
}
