<?php

declare(strict_types=1);

namespace MailerLite\LaravelElasticsearch\Console\Command;

use MailerLite\LaravelElasticsearch\Manager;
use Illuminate\Console\Command;

final class IndexExistsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'laravel-elasticsearch:utils:index-exists
                            {index-name : The index name}';

    /**
     * @var Manager
     */
    private $manager;

    public function __construct(
        Manager $manager
    ) {
        $this->manager = $manager;

        parent::__construct();
    }

    public function handle(): int
    {
        $indexName = $this->argument('index-name');

        if ($indexName === null ||
            !is_string($indexName) ||
            mb_strlen($indexName) === 0
        ) {
            $this->output->writeln(
                '<error>Argument index-name must be a non empty string.</error>'
            );

            return self::FAILURE;
        }

        if ($this->manager->indices()->exists([
            'index' => $indexName,
        ])->asBool()) {
            $this->output->writeln(
                sprintf(
                    '<info>Index %s exists.</info>',
                    $indexName
                )
            );

            return self::SUCCESS;
        } else {
            $this->output->writeln(
                sprintf(
                    '<comment>Index %s doesn\'t exists.</comment>',
                    $indexName
                )
            );

            return self::SUCCESS;
        }
    }
}
