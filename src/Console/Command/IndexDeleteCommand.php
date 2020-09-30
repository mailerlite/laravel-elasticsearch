<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Console\Command;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Throwable;


final class IndexDeleteCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'laravel-elasticsearch:utils:index-delete
                            {index-name : The index name}';

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

        if (!$this->argumentIsValid($indexName)) {
            return self::FAILURE;
        }

        if (!$this->client->indices()->exists([
            'index' => $indexName,
        ])) {
            $this->output->writeln(
                sprintf(
                    '<error>Index %s doesn\'t exists and cannot be deleted.</error>',
                    $indexName
                )
            );

            return self::FAILURE;
        }

        try {
            $this->client->indices()->delete([
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
