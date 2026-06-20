<?php

declare(strict_types=1);

namespace MailerLite\LaravelElasticsearch\Tests\Console\Command;

use MailerLite\LaravelElasticsearch\Tests\TestCase;
use MailerLite\LaravelElasticsearch\Manager;
use Elastic\Elasticsearch\Endpoints\Indices;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Mockery\MockInterface;

final class IndexExistsCommandTest extends TestCase
{
    public function testIndexExists(): void
    {
        $this->mock(Manager::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(Indices::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn($this->elasticsearchResponse(true));
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
        $this->mock(Manager::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(Indices::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn($this->elasticsearchResponse(false));
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-exists',
            ['index-name' => 'test_index_name_doesnt_exists']
        )->assertExitCode(0)
            ->expectsOutput('Index test_index_name_doesnt_exists doesn\'t exists.');
    }

    #[DataProvider('invalidIndexNameDataProvider')]
    public function testArgumentIndexNameIsInValid($invalidIndexName): void
    {
        $this->artisan('laravel-elasticsearch:utils:index-exists',
            ['index-name' => $invalidIndexName]
        )->assertExitCode(1)
            ->expectsOutput('Argument index-name must be a non empty string.');
    }

    public static function invalidIndexNameDataProvider(): Generator
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
