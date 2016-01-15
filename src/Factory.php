<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\ClientBuilder;

class Factory {

	/**
	 * Make the Elasticsearch client for the given named configuration, or
	 * the default client.
	 *
	 * @param array $config
	 * @return \Elasticsearch\Client|mixed
	 */
	public function make(array $config) {

		// Build the client
		return $this->buildClient($config);

	}

	/**
	 * Build and configure an Elasticsearch client.
	 *
	 * @param array $config
	 * @return \Elasticsearch\Client
	 */
	protected function buildClient(array $config) {

		$clientBuilder = ClientBuilder::create();

		// Configure hosts

		$clientBuilder->setHosts($config['hosts']);

		// Configure SSL

		if (isset($config['sslVerification']) && $config['sslVerification'] !== null) {
			$clientBuilder->setSSLVerification($config['sslVerification']);
		}

		// Configure logging

		if (isset($config['logging']) && $config['logging']) {
			if (isset($config['logObject']) && $config['logObject'] instanceof LoggerInterface) {
				$clientBuilder->setLogger($config['logObject']);
			} else if (isset($config['logPath']) && isset($config['logLevel'])) {
				$path = $config['logPath'];
				$level = $config['logLevel'];
				$logger = ClientBuilder::defaultLogger($path, $level);
				$clientBuilder->setLogger($logger);
			}
		}

		// Configure Sniff-on-Start

		if (isset($config['sniffOnStart']) && $config['sniffOnStart'] !== null) {
			$clientBuilder->setSniffOnStart($config['sniffOnStart']);
		}

		// Configure Retries

		if (isset($config['retries']) && $config['retries'] !== null) {
			$clientBuilder->setRetries($config['retries']);
		}

		// Configure HTTP Handler

		if (isset($config['httpHandler']) && $config['httpHandler'] !== null) {
			$clientBuilder->setHandler($config['httpHandler']);
		}

		// Configure Connection Pool

		if (isset($config['connectionPool']) && $config['connectionPool'] !== null) {
			$clientBuilder->setConnectionPool($config['connectionPool']);
		}

		// Configure Connection Selector

		if (isset($config['connectionSelector']) && $config['connectionSelector'] !== null) {
			$clientBuilder->setSelector($config['connectionSelector']);
		}

		// Configure Serializer

		if (isset($config['serializer']) && $config['serializer'] !== null) {
			$clientBuilder->setSerializer($config['serializer']);
		}

		// Configure Connection Factory

		if (isset($config['connectionFactory']) && $config['connectionFactory'] !== null) {
			$clientBuilder->setConnectionFactory($config['connectionFactory']);
		}

		// Configure Endpoint

		if (isset($config['endpoint']) && $config['endpoint'] !== null) {
			$clientBuilder->setEndpoint($config['endpoint']);
		}

		// Build and return the client

		return $clientBuilder->build();
	}

}
