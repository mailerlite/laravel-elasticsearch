<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Tests\Console\Command;

use Cviebrock\LaravelElasticsearch\Tests\TestCase;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Exception;
use Generator;
use Mockery\MockInterface;

final class IndexDeleteCommandTest extends TestCase
{
    public function testIndexDeleteMustSucceed(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->andReturn(true);

                        $mock->shouldReceive('delete')
                            ->once()
                            ->andReturn([]);
                    })
                );
        });

        $this->artisan('laravel-elasticsearch:utils:index-delete',
            ['index-name' => 'valid_index_name']
        )->assertExitCode(0)
            ->expectsOutput('Index valid_index_name deleted.');
    }

    public function testIndexDeleteMustFail(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->andReturn(true);

                        $mock->shouldReceive('delete')
                            ->once()
                            ->andThrow(
                                new Exception('error creating index test exception')
                            );
                    })
                );
        });

        $this->artisan('laravel-elasticsearch:utils:index-delete',
            ['index-name' => 'valid_index_name']
        )->assertExitCode(1)
            ->expectsOutput('Error deleting index valid_index_name, exception message: error creating index test exception.');
    }

    public function testIndexDeleteMustFailBecauseIndexDoesntExists(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->andReturn(false);

                        $mock->shouldNotReceive('create');
                    })
                );
        });

        $this->artisan('laravel-elasticsearch:utils:index-delete',
            ['index-name' => 'valid_index_name']
        )->assertExitCode(1)
            ->expectsOutput('Index valid_index_name doesn\'t exists and cannot be deleted.');
    }

    /**
     * @dataProvider invalidIndexNameDataProvider
     */
    public function testArgumentIndexNameIsInValid($invalidIndexName): void
    {
        $this->artisan('laravel-elasticsearch:utils:index-delete',
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
