<?php namespace Cviebrock\LaravelElasticsearch\Tests;

use Cviebrock\LaravelElasticsearch\Factory;
use Cviebrock\LaravelElasticsearch\Manager;
use Elasticsearch as ElasticsearchFacade;
use Elasticsearch\Client;


class ServiceProviderTests extends TestCase
{

    public function testAbstractsAreLoaded()
    {
        $factory = app('elasticsearch.factory');
        $this->assertInstanceOf(Factory::class, $factory);

        $manager = app('elasticsearch');
        $this->assertInstanceOf(Manager::class, $manager);

        $client = app(Client::class);
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testFacadeWorks() {
        $info = ElasticsearchFacade::info();

        var_dump($info);
        $this->assertTrue(is_array($info));
    }

}
