<?php

declare(strict_types=1);

namespace MailerLite\LaravelElasticsearch\Console\Command;

use MailerLite\LaravelElasticsearch\Manager;
use Illuminate\Console\Command;
use Throwable;

final class IndexDeleteCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'laravel-elasticsearch:utils:index-delete
                            {index-name : The index name}';

    public function handle(Manager $manager): int
    {
        $indexName = $this->argument('index-name');

        if (!$this->argumentIsValid($indexName)) {
            return self::FAILURE;
        }

        if (!$manager->indices()->exists([
            'index' => $indexName,
        ])->asBool()) {
            $this->output->writeln(
                sprintf(
                    '<error>Index %s doesn\'t exists and cannot be deleted.</error>',
                    $indexName
                )
            );

            return self::FAILURE;
        }

        try {
            $manager->indices()->delete([
                'index' => $this->argument('index-name'),
            ]);
        } catch (Throwable $exception) {
            $this->output->writeln(
                sprintf(
                    '<error>Error deleting index %s, exception message: %s.</error>',
                    $indexName,
                    $exception->getMessage()
                )
            );

            return self::FAILURE;
        }

        $this->output->writeln(
            sprintf(
                '<info>Index %s deleted.</info>',
                $indexName
            )
        );

        return self::SUCCESS;
    }

    private function argumentIsValid($indexName): bool
    {
        if ($indexName === null ||
            !is_string($indexName) ||
            mb_strlen($indexName) === 0
        ) {
            $this->output->writeln(
                '<error>Argument index-name must be a non empty string.</error>'
            );

            return false;
        }

        return true;
    }
}
