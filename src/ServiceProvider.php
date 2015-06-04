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

		$this->app->singleton('elasticsearch', function($app)
		{
			return (new Factory($app))->make();
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {

	}
}
