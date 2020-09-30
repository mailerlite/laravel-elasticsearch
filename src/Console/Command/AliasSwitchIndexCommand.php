<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Console\Command;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Throwable;


final class AliasSwitchIndexCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'laravel-elasticsearch:utils:alias-switch-index
                            {new-index-name : The new index name}
                            {old-index-name : The old index name}
                            {alias-name : The alias name}';

    /**
     * @var Client
     */
    private $client;

    public function __construct(
        Client $client
    ) {
        $this->client = $client;

        parent::__construct();
    }

    public function handle(): int
    {
        $newIndexName = $this->argument('new-index-name');
        $oldIndexName = $this->argument('old-index-name');
        $aliasName = $this->argument('alias-name');

        if (!$this->argumentsAreValid(
            $newIndexName,
            $oldIndexName,
            $aliasName
        )) {
            return self::FAILURE;
        }

        if (!$this->client->indices()->exists([
            'index' => $newIndexName,
        ])) {
            $this->output->writeln(
                sprintf(
                    '<error>Index %s cannot be linked to alias because doesn\'t exists.</error>',
                    $newIndexName
                )
            );

            return self::FAILURE;
        }

        try {
            $this->client->indices()->putAlias([
                'index' => $newIndexName,
                'name'  => $aliasName,
            ]);

            $this->client->indices()->deleteAlias([
                'index' => $oldIndexName,
                'name'  => $aliasName,
            ]);
        } catch (Throwable $exception) {
            $this->output->writeln(
                sprintf(
                    '<error>Error switching indexes - new index: %s, old index: %s in alias %s, exception message: %s.</error>',
                    $newIndexName,
                    $oldIndexName,
                    $aliasName,
                    $exception->getMessage()
                )
            );

            return self::FAILURE;
        }

        $this->output->writeln(
            sprintf(
                '<info>New index %s linked and old index %s removed from alias %s.</info>',
                $newIndexName,
                $oldIndexName,
                $aliasName
            )
        );

        return self::SUCCESS;
    }

    private function argumentsAreValid($newIndexName, $oldIndexName, $aliasName): bool
    {
        if ($newIndexName === null ||
            !is_string($newIndexName) ||
            mb_strlen($newIndexName) === 0
        ) {
            $this->output->writeln(
                '<error>Argument new-index-name must be a non empty string.</error>'
            );

            return false;
        }

        if ($oldIndexName === null ||
            !is_string($oldIndexName) ||
            mb_strlen($oldIndexName) === 0
        ) {
            $this->output->writeln(
                '<error>Argument old-index-name must be a non empty string.</error>'
            );

            return false;
        }

        if ($aliasName === null ||
            !is_string($aliasName) ||
            mb_strlen($aliasName) === 0
        ) {
            $this->output->writeln(
                '<error>Argument alias-name must be a non empty string.</error>'
            );

            return false;
        }

        return true;
    }
}
