<?php namespace Cviebrock\LaravelElasticsearch;

use Illuminate\Container\Container;


class Manager {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var ConnectionFactory
	 */
	protected $factory;

	/**
	 * The active connection instances.
	 *
	 * @var array
	 */
	protected $connections = [];

	/**
	 * @param Container $container
	 * @param Factory $factory
	 */
	public function __construct(Container $container, Factory $factory) {
		$this->container = $container;
		$this->factory = $factory;
	}

	public function connection($name = null) {

		// If we haven't created this connection, we'll create it based on the config
		// provided in the application.

		if (!isset($this->connections[$name])) {
			$this->connections[$name] = $this->makeConnection($name);
		}

		return $this->connections[$name];
	}


	protected function makeConnection($name) {
		$config = $this->getConfig($name);

		return $this->factory->make($config, $name);
	}

	protected function getConfig($name) {
		$name = $name ?: $this->getDefaultConnection();

		// Load configuration (L4- and L5-friendly)
		$connections = $this->container['config']->get('elasticsearch.connections') ?: $this->container['config']->get('elasticsearch::config.connections');

		if (is_null($config = array_get($connections, $name))) {
			throw new \InvalidArgumentException("Connection [$name] not configured.");
		}

		return $config;
	}

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection() {
		return $this->container['config']->get('elasticsearch.default') ?: $this->container['config']->get('elasticsearch::config.default');
	}
}
