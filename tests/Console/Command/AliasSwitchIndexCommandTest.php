<?php

declare(strict_types=1);

namespace Cviebrock\LaravelElasticsearch\Tests\Console\Command;

use Cviebrock\LaravelElasticsearch\Tests\TestCase;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Exception;
use Generator;
use Mockery\MockInterface;

final class AliasSwitchIndexCommandTest extends TestCase
{
    public function testSwitchIndexMustSucceed(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(3)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(true);

                        $mock->shouldReceive('putAlias')
                            ->once()
                            ->andReturn([]);

                        $mock->shouldReceive('deleteAlias')
                            ->once()
                            ->andReturn([]);
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:alias-switch-index',
            [
                'new-index-name' => 'new_valid_index_name',
                'old-index-name' => 'old_valid_index_name',
                'alias-name' => 'valid_alias_name',
            ]
        )->assertExitCode(0)
            ->expectsOutput(
                'New index new_valid_index_name linked and old index old_valid_index_name removed from alias valid_alias_name.'
            );
    }

    public function testSwitchIndexMustFailBecauseNewIndexDoesntExists(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->once()
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(false);

                        $mock->shouldNotReceive('putAlias');

                        $mock->shouldNotReceive('deleteAlias');
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:alias-switch-index',
            [
                'new-index-name' => 'new_valid_index_name',
                'old-index-name' => 'old_valid_index_name',
                'alias-name' => 'valid_alias_name',
            ]
        )->assertExitCode(1)
            ->expectsOutput(
                'Index new_valid_index_name cannot be linked to alias because doesn\'t exists.'
            );
    }

    public function testSwitchIndexMustFailDueToPutAliasException(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(2)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(true);

                        $mock->shouldReceive('putAlias')
                            ->once()
                            ->andThrow(
                                new Exception(
                                    'error adding new index to alias exception'
                                )
                            );

                        $mock->shouldNotReceive('deleteAlias');
                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:alias-switch-index',
            [
                'new-index-name' => 'new_valid_index_name',
                'old-index-name' => 'old_valid_index_name',
                'alias-name' => 'valid_alias_name',
            ]
        )->assertExitCode(1)
            ->expectsOutput(
                'Error switching indexes - new index: new_valid_index_name, old index: old_valid_index_name in alias valid_alias_name, exception message: error adding new index to alias exception.'
            );
    }

    public function testSwitchIndexMustFailDueToDeleteAliasException(): void
    {
        $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('indices')
                ->times(3)
                ->andReturn(
                    $this->mock(IndicesNamespace::class, function (MockInterface $mock) {
                        $mock->shouldReceive('exists')
                            ->once()
                            ->andReturn(true);

                        $mock->shouldReceive('putAlias')
                            ->once()
                            ->andReturn([]);

                        $mock->shouldReceive('deleteAlias')
                            ->once()
                            ->andThrow(
                                new Exception(
                                    'error removing old index from alias exception'
                                )
                            );

                    })
                );
        });

        $this->artisan(
            'laravel-elasticsearch:utils:alias-switch-index',
            [
                'new-index-name' => 'new_valid_index_name',
                'old-index-name' => 'old_valid_index_name',
                'alias-name' => 'valid_alias_name',
            ]
        )->assertExitCode(1)
            ->expectsOutput(
                'Error switching indexes - new index: new_valid_index_name, old index: old_valid_index_name in alias valid_alias_name, exception message: error removing old index from alias exception.'
            );
    }

    /**
     * @dataProvider invalidIndexNameDataProvider
     */
    public function testArgumentIndexNameAndAliasAreInValid(
        $invalidNewIndexName,
        $invalidOldIndexName,
        $invalidAliasName,
        string $expectedOutputMessage
    ): void {
        $this->artisan('laravel-elasticsearch:utils:alias-switch-index',
            [
                'new-index-name' => $invalidNewIndexName,
                'old-index-name' => $invalidOldIndexName,
                'alias-name' => $invalidAliasName,
            ]
        )->assertExitCode(1)
            ->expectsOutput($expectedOutputMessage);
    }

    public function invalidIndexNameDataProvider(): Generator
    {
        yield [
            null,
            'valid_old_index_name',
            'valid_alias_name',
            'Argument new-index-name must be a non empty string.'
        ];

        yield [
            '',
            'valid_old_index_name',
            'valid_alias_name',
            'Argument new-index-name must be a non empty string.'
        ];

        yield [
            true,
            'valid_old_index_name',
            'valid_alias_name',
            'Argument new-index-name must be a non empty string.'
        ];

        yield [
            1,
            'valid_old_index_name',
            'valid_alias_name',
            'Argument new-index-name must be a non empty string.'
        ];

        yield [
            [],
            'valid_old_index_name',
            'valid_alias_name',
            'Argument new-index-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            null,
            'valid_alias_name',
            'Argument old-index-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            '',
            'valid_alias_name',
            'Argument old-index-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            true,
            'valid_alias_name',
            'Argument old-index-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            1,
            'valid_alias_name',
            'Argument old-index-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            [],
            'valid_alias_name',
            'Argument old-index-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            'valid_old_index_name',
            null,
            'Argument alias-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            'valid_old_index_name',
            '',
            'Argument alias-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            'valid_old_index_name',
            true,
            'Argument alias-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            'valid_old_index_name',
            1,
            'Argument alias-name must be a non empty string.'
        ];

        yield [
            'valid_new_index_name',
            'valid_old_index_name',
            [],
            'Argument alias-name must be a non empty string.'
        ];
    }
}
