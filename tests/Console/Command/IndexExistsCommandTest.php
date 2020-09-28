<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Tests\Console\Command;

use Cviebrock\LaravelElasticsearch\Tests\TestCase;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Generator;
use Mockery\MockInterface;

final class IndexExistsCommandTest extends TestCase
{
    public function testIndexExists(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(true);
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-exists',
            ['index-name' => 'index_name_exists']
        )->assertExitCode(0)
            ->expectsOutput('Index index_name_exists exists.');
    }

    public function testIndexDoesntExists(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(false);
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-exists',
            ['index-name' => 'test_index_name_doesnt_exists']
        )->assertExitCode(0)
            ->expectsOutput('Index test_index_name_doesnt_exists doesn\'t exists.');
    }

    /**
     * @dataProvider invalidIndexNameDataProvider
     */
    public function testArgumentIndexNameIsInValid($invalidIndexName): void
    {
        $this->artisan('laravel-elasticsearch:utils:index-exists',
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
