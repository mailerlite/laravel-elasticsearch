<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\ClientBuilder;
use Illuminate\Container\Container;


class Factory {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var array
	 */
	protected $config;

	public function __construct(Container $container) {
		$this->container = $container;
		$this->config = $this->container['config']->get('elasticsearch') ?: $this->container['config']->get('elasticsearch::config');
	}

	public function make($name) {

		// Do we already have a bound instance of this client?
		$key = 'elasticsearch.clients.' . $name;

		if ($this->container->bound($key)) {
			return $this->container->make($key);
		}

		// Build the client
		$client = $this->buildClient($this->getConnectionConfig($name));

		// Persist it in the container so we don't need to build it every time
		// and return it.

		$this->container->instance($key, $client);

		return $client;
	}

	protected function buildClient(array $config) {

		$clientBuilder = ClientBuilder::create();

		// Configure hosts

		$clientBuilder->setHosts($config['hosts']);

		// Configure SSL

		if ($config['sslVerification'] !== null) {
			$clientBuilder->setSSLVerification($config['sslVerification']);
		}

		// Configure logging

		if ($config['logging']) {
			if ($config['logObject'] instanceof LoggerInterface) {
				$clientBuilder->setLogger($config['logObject']);
			} else {
				$path = $config['logPath'];
				$level = $config['logLevel'];
				$logger = ClientBuilder::defaultLogger($path, $level);
				$clientBuilder->setLogger($logger);
			}
		}

		// Configure Sniff-on-Start

		if ($config['sniffOnStart'] !== null) {
			$clientBuilder->setSniffOnStart($config['sniffOnStart']);
		}

		// Configure Retries

		if ($config['retries'] !== null) {
			$clientBuilder->setRetries($config['retries']);
		}

		// Configure HTTP Handler

		if ($config['httpHandler'] !== null) {
			$clientBuilder->setHandler($config['httpHandler']);
		}

		// Configure Connection Pool

		if ($config['connectionPool'] !== null) {
			$clientBuilder->setConnectionPool($config['connectionPool']);
		}

		// Configure Connection Selector

		if ($config['connectionSelector'] !== null) {
			$clientBuilder->setSelector($config['connectionSelector']);
		}

		// Configure Serializer

		if ($config['serializer'] !== null) {
			$clientBuilder->setSerializer($config['serializer']);
		}

		// Configure Connection Factory

		if ($config['connectionFactory'] !== null) {
			$clientBuilder->setConnectionFactory($config['connectionFactory']);
		}

		// Configure Endpoint

		if ($config['endpoint'] !== null) {
			$clientBuilder->setEndpoint($config['endpoint']);
		}

		// Build and return the client

		return $clientBuilder->build();
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	protected function getConnectionConfig($name) {

		if(!empty($this->config['connections'][$name])) {
			return $this->config['connections'][$name];
		}

		return $this->config['connections']['default'];
	}
}
