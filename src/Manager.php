<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\Client;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;


/**
 * Class Manager
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class Manager
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * The Elasticsearch connection factory instance.
     *
     * @var \Cviebrock\LaravelElasticsearch\Factory
     */
    protected $factory;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * @param \Illuminate\Contracts\Container\Container $app
     * @param \Cviebrock\LaravelElasticsearch\Factory $factory
     */
    public function __construct(Container $app, Factory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

    /**
     * Retrieve or build the named connection.
     *
     * @param string|null $name
     *
     * @return \Elasticsearch\Client
     */
    public function connection(string $name = null): Client
    {
        $name = $name ?: $this->getDefaultConnection();

        if (!isset($this->connections[$name])) {
            $client = $this->makeConnection($name);

            $this->connections[$name] = $client;
        }

        return $this->connections[$name];
    }

    /**
     * Get the default connection.
     *
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->app['config']['elasticsearch.defaultConnection'];
    }

    /**
     * Set the default connection.
     *
     * @param string $connection
     */
    public function setDefaultConnection(string $connection): void
    {
        $this->app['config']['elasticsearch.defaultConnection'] = $connection;
    }

    /**
     * Make a new connection.
     *
     * @param string $name
     *
     * @return \Elasticsearch\Client
     */
    protected function makeConnection(string $name): Client
    {
        $config = $this->getConfig($name);

        return $this->factory->make($config);
    }

    /**
     * Get the configuration for a named connection.
     *
     * @param $name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getConfig(string $name)
    {
        $connections = $this->app['config']['elasticsearch.connections'];

        if (null === $config = Arr::get($connections, $name)) {
            throw new \InvalidArgumentException("Elasticsearch connection [$name] not configured.");
        }

        return $config;
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
