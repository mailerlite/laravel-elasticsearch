<?php

declare(strict_types=1);

namespace MailerLite\LaravelElasticsearch\Console\Command;

use MailerLite\LaravelElasticsearch\Manager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Throwable;

final class IndexCreateOrUpdateMappingCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'laravel-elasticsearch:utils:index-create-or-update-mapping
                            {index-name : The index name}
                            {mapping-file-path : The absolute path where mapping file is located}';

    public function handle(Manager $manager, Filesystem $filesystem): int
    {
        $indexName = $this->argument('index-name');
        $mappingFilePath = $this->argument('mapping-file-path');

        if (!$this->argumentsAreValid(
            $indexName,
            $mappingFilePath,
            $filesystem
        )) {
            return self::FAILURE;
        }

        if (!$manager->indices()->exists([
            'index' => $indexName,
        ])->asBool()) {
            try {
                $manager->indices()->create([
                    'index' => $indexName,
                    'body'  => json_decode(
                        $filesystem->get($mappingFilePath),
                        true
                    ),
                ]);
            } catch (Throwable $exception) {
                $this->output->writeln(
                    sprintf(
                        '<error>Error creating or updating mapping for index %s, given mapping file: %s - error message: %s.</error>',
                        $indexName,
                        $mappingFilePath,
                        $exception->getMessage()
                    )
                );

                return self::FAILURE;
            }

            $this->output->writeln(
                sprintf(
                    '<info>Index %s doesn\'t exist, a new index was created with mapping/settings using file %s.</info>',
                    $indexName,
                    $mappingFilePath
                )
            );

            return self::SUCCESS;
        }

        try {
            $manager->indices()->putMapping([
                'index' => $indexName,
                'body'  => json_decode(
                    $filesystem->get($mappingFilePath),
                    true
                ),
            ]);
        } catch (Throwable $exception) {
            $this->output->writeln(
                sprintf(
                    '<error>Error creating or updating mapping for index %s, given mapping file: %s - error message: %s.</error>',
                    $indexName,
                    $mappingFilePath,
                    $exception->getMessage()
                )
            );

            return self::FAILURE;
        }

        $this->output->writeln(
            sprintf(
                '<info>Mapping created or updated for index %s using file %s.</info>',
                $indexName,
                $mappingFilePath
            )
        );

        return self::SUCCESS;
    }

    private function argumentsAreValid($indexName, $mappingFilePath, Filesystem $filesystem): bool
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

        if ($mappingFilePath === null ||
            !is_string($mappingFilePath) ||
            mb_strlen($mappingFilePath) === 0 ||
            !$filesystem->exists($mappingFilePath)
        ) {
            $this->output->writeln(
                '<error>Argument mapping-file-path must exists on filesystem and must be a non empty string.</error>'
            );

            return false;
        }

        return true;
    }
}
