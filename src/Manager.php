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


	/**
	 * @param $name
	 * @return mixed
	 */
	protected function makeConnection($name) {

		return $this->factory->make($name);
	}

}
