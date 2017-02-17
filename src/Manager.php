<?php namespace Cviebrock\LaravelElasticsearch;

use Illuminate\Contracts\Foundation\Application;


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
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The Elasticsearch connection factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * @param Application $app
     * @param Factory $factory
     */
    public function __construct(Application $app, Factory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

    /**
     * Retrieve or build the named connection.
     *
     * @param null $name
     * @return \Elasticsearch\Client|mixed
     */
    public function connection($name = null)
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
    public function getDefaultConnection()
    {
        return $this->app['config']['elasticsearch.defaultConnection'];
    }

    /**
     * Set the default connection.
     *
     * @param string $connection
     */
    public function setDefaultConnection($connection)
    {
        $this->app['config']['elasticsearch.defaultConnection'] = $connection;
    }

    /**
     * Make a new connection.
     *
     * @param $name
     * @return \Elasticsearch\Client|mixed
     */
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);

        return $this->factory->make($config);
    }

    /**
     * Get the configuration for a named connection.
     *
     * @param $name
     * @return mixed
     */
    protected function getConfig($name)
    {
        $connections = $this->app['config']['elasticsearch.connections'];

        if (is_null($config = array_get($connections, $name))) {
            throw new \InvalidArgumentException("Elasticsearch connection [$name] not configured.");
        }

        return $config;
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
