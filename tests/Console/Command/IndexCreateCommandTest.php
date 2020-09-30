<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Tests\Console\Command;

use Cviebrock\LaravelElasticsearch\Tests\TestCase;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Exception;
use Generator;
use Mockery\MockInterface;

final class IndexCreateCommandTest extends TestCase
{
    public function testCreateIndexMustSucceed(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->andReturn(false);

                        $mock->shouldReceive('create')
                            ->once()
                            ->andReturn([]);
                    })
                );
        });

        $this->artisan('laravel-elasticsearch:utils:index-create',
            ['index-name' => 'valid_index_name']
        )->assertExitCode(0)
            ->expectsOutput('Index valid_index_name created.');
    }

    public function testCreateIndexMustFail(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->andReturn(false);

                        $mock->shouldReceive('create')
                            ->once()
                            ->andThrow(
                                new Exception('index already exists test exception')
                            );
                    })
                );
        });

        $this->artisan('laravel-elasticsearch:utils:index-create',
            ['index-name' => 'valid_index_name']
        )->assertExitCode(1)
            ->expectsOutput('Error creating index valid_index_name, exception message: index already exists test exception.');
    }

    public function testCreateIndexMustFailBecauseIndexAlreadyExists(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->andReturn(true);

                        $mock->shouldNotReceive('create');
                    })
                );
        });

        $this->artisan('laravel-elasticsearch:utils:index-create',
            ['index-name' => 'valid_index_name']
        )->assertExitCode(1)
            ->expectsOutput('Index valid_index_name already exists and cannot be created.');
    }

    /**
     * @dataProvider invalidIndexNameDataProvider
     */
    public function testArgumentIndexNameIsInValid($invalidIndexName): void
    {
        $this->artisan('laravel-elasticsearch:utils:index-create',
            ['index-name' => $invalidIndexName]
        )->assertExitCode(1)
            ->expectsOutput('Argument index-name must be a non empty string.');
    }

    public function invalidIndexNameDataProvider(): Generator
    {
        yield [
            null
        ];

        yield [
            ''
        ];

        yield [
            true
        ];

        yield [
            1
        ];

        yield [
            []
        ];
    }
}
