<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Tests\Console\Command;

use Cviebrock\LaravelElasticsearch\Tests\TestCase;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Exception;
use Generator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery\MockInterface;

final class IndexCreateOrUpdateMappingCommandTest extends TestCase
{
    public function testCreateOrUpdateMappingMustSucceed(): void
    {
        $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->shouldReceive('exists')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('get')
                ->once()
                ->andReturn('{}');
        });

        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(true);

                        $mock->shouldReceive('putMapping')
                            ->once()
                            ->andReturn([]);
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-create-or-update-mapping',
            [
                'index-name' => 'valid_index_name',
                'mapping-file-path' => '/path/to/existing_mapping_file.json',
            ]
        )->assertExitCode(0)
            ->expectsOutput('Mapping created or updated for index valid_index_name using file /path/to/existing_mapping_file.json.');
    }

    public function testCreateOrUpdateMappingMustFailBecauseMappingFileDoesntExistsOnFilesystem(): void
    {
        $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->shouldReceive('exists')
                ->once()
                ->andReturn(false);
        });

        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('indices');
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-create-or-update-mapping',
            [
                'index-name' => 'valid_index_name',
                'mapping-file-path' => '/path/to/non_existing_mapping_file.json',
            ]
        )->assertExitCode(1)
            ->expectsOutput('Argument mapping-file-path must exists on filesystem and must be a non empty string.');
    }

    public function testCreateOrUpdateMappingMustCreateNewIndexIfIndexDoesntExists(): void
    {
        $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->shouldReceive('exists')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('get')
                ->once()
                ->andReturn('{}');
        });

        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(false);

                        $mock->shouldReceive('create')
                            ->once()
                            ->andReturn(true);
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-create-or-update-mapping',
            [
                'index-name' => 'valid_index_name',
                'mapping-file-path' => '/path/to/existing_mapping_file.json',
            ]
        )->assertExitCode(0)
            ->expectsOutput('Index valid_index_name doesn\'t exist, a new index was created with mapping/settings using file /path/to/existing_mapping_file.json.');
    }

    public function testCreateOrUpdateMappingMustFail(): void
    {
        $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->shouldReceive('exists')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('get')
                ->once()
                ->andReturn('{}');
        });

        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(true);

                        $mock->shouldReceive('putMapping')
                            ->once()
                            ->andThrow(
                                new Exception('error creating or updating mapping test exception')
                            );
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:index-create-or-update-mapping',
            [
                'index-name' => 'valid_index_name',
                'mapping-file-path' => '/path/to/existing_mapping_file.json',
            ]
        )->assertExitCode(1)
            ->expectsOutput(
                'Error creating or updating mapping for index valid_index_name, given mapping file: /path/to/existing_mapping_file.json - error message: error creating or updating mapping test exception.'
            );
    }

    /**
     * @dataProvider invalidIndexNameDataProvider
     */
    public function testArgumentIndexNameAndAliasAreInValid(
        $invalidIndexName,
        $invalidAliasName,
        string $expectedOutputMessage
    ): void {
        $this->artisan('laravel-elasticsearch:utils:index-create-or-update-mapping',
            [
                'index-name' => $invalidIndexName,
                'mapping-file-path' => $invalidAliasName,
            ]
        )->assertExitCode(1)
            ->expectsOutput($expectedOutputMessage);
    }

    public function invalidIndexNameDataProvider(): Generator
    {
        yield [
            null,
            '/valid/path/mapping.json',
            'Argument index-name must be a non empty string.'
        ];

        yield [
            '',
            '/valid/path/mapping.json',
            'Argument index-name must be a non empty string.'
        ];

        yield [
            true,
            '/valid/path/mapping.json',
            'Argument index-name must be a non empty string.'
        ];

        yield [
            1,
            '/valid/path/mapping.json',
            'Argument index-name must be a non empty string.'
        ];

        yield [
            [],
            '/valid/path/mapping.json',
            'Argument index-name must be a non empty string.'
        ];

        yield [
            'valid_index_name',
            null,
            'Argument mapping-file-path must exists on filesystem and must be a non empty string.'
        ];

        yield [
            'valid_index_name',
            '',
            'Argument mapping-file-path must exists on filesystem and must be a non empty string.'
        ];

        yield [
            'valid_index_name',
            true,
            'Argument mapping-file-path must exists on filesystem and must be a non empty string.'
        ];

        yield [
            'valid_index_name',
            1,
            'Argument mapping-file-path must exists on filesystem and must be a non empty string.'
        ];

        yield [
            'valid_index_name',
            [],
            'Argument mapping-file-path must exists on filesystem and must be a non empty string.'
        ];
    }
}
