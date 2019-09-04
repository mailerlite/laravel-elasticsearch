<?php namespace Cviebrock\LaravelElasticsearch\Tests;

use Cviebrock\LaravelElasticsearch\Factory;
use Cviebrock\LaravelElasticsearch\Manager;
use Elasticsearch;
use Elasticsearch\Client;


class ServiceProviderTests extends TestCase
{

    public function testAbstractsAreLoaded(): void
    {
        $factory = app('elasticsearch.factory');
        $this->assertInstanceOf(Factory::class, $factory);

        $manager = app('elasticsearch');
        $this->assertInstanceOf(Manager::class, $manager);

        $client = app(Client::class);
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * Test that the facade works.
     * @todo This seems a bit simplistic ... maybe a better way to do this?
     */
    public function testFacadeWorks(): void
    {
        $ping = Elasticsearch::ping();

        $this->assertTrue($ping);
    }

    /**
     * Test we can get the ES info.
     */
    public function testInfoWorks(): void
    {
        $info = Elasticsearch::info();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('cluster_name', $info);
    }
}
