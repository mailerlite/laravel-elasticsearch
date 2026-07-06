<?php 

namespace MailerLite\LaravelElasticsearch\Tests;

use Elastic\Elasticsearch\Response\Elasticsearch;
use MailerLite\LaravelElasticsearch\Facade;
use MailerLite\LaravelElasticsearch\ServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends Orchestra
{
    /**
     * Build a mocked Elasticsearch (8.x) response whose asBool() returns the
     * given value. The 8.x client returns response objects instead of scalars,
     * so endpoints like indices()->exists() must be resolved with ->asBool().
     */
    protected function elasticsearchResponse(bool $value): Elasticsearch
    {
        return Mockery::mock(Elasticsearch::class, ['asBool' => $value]);
    }

    /**
     * @inheritdoc
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getPackageAliases($app)
    {
        return [
            'Elasticsearch' => Facade::class,
        ];
    }
}
