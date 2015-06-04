<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Psr\Log\LoggerInterface;


/**
 * Class ServiceProvider
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class ServiceProvider extends BaseServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {

		$app = $this->app;

		if (version_compare($app::VERSION, '5.0') < 0) {
			// Laravel 4
			$this->package('cviebrock/laravel-elasticsearch', 'elasticsearch', realpath(__DIR__));
		} else {
			// Laravel 5
			$configPath = realpath(__DIR__ . '/config/config.php');
			$this->publishes([
				$configPath => config_path('elasticsearch.php')
			]);
			$this->mergeConfigFrom($configPath, 'elasticsearch');
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		$this->app->singleton('elasticsearch', function ($app) {

			$config = $app['config']->get('elasticsearch') ?: $app['config']->get('elasticsearch::config');

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

			// Build the client

			$client = $clientBuilder->build();

			// If we are using index-prefixing, then generate a prefix based on the
			// current environment.

			if ($config['environmentIndexPrefixing']) {
				$environment = strtolower($app->environment());
				$prefix = trim(preg_replace('/[^a-z0-9]+/', '_', $environment), '_') . '_';
			} else {
				$prefix = null;
			}

			// Wrap the base client in our client

			$client = new Client($client, $prefix);

			// Return the client

			return $client;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {

		return ['elasticsearch'];
	}
}
