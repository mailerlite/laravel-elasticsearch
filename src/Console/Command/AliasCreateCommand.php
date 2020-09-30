<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Console\Command;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Throwable;


final class AliasCreateCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'laravel-elasticsearch:utils:alias-create
                            {index-name : The index name}
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
        $indexName = $this->argument('index-name');
        $aliasName = $this->argument('alias-name');

        if (!$this->argumentsAreValid(
            $indexName,
            $aliasName
        )) {
            return self::FAILURE;
        }

        if (!$this->client->indices()->exists([
            'index' => $indexName,
        ])) {
            $this->output->writeln(
                sprintf(
                    '<error>Index %s doesn\'t exists and alias cannot be created.</error>',
                    $indexName
                )
            );

            return self::FAILURE;
        }

        try {
            $this->client->indices()->putAlias([
                'index' => $indexName,
                'name'  => $aliasName,
            ]);
        } catch (Throwable $exception) {
            $this->output->writeln(
                sprintf(
                    '<error>Error creating alias %s for index %s, exception message: %s.</error>',
                    $aliasName,
                    $indexName,
                    $exception->getMessage()
                )
            );

            return self::FAILURE;
        }

        $this->output->writeln(
            sprintf(
                '<info>Alias %s created for index %s.</info>',
                $aliasName,
                $indexName
            )
        );

        return self::SUCCESS;
    }

    private function argumentsAreValid($indexName, $aliasName): bool
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
