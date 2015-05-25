<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\Client;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;


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
			$this->package('cviebrock/laravel-elasticsearch', 'elasticsearch', realpath(__DIR__ . '/../'));
		} else {
			// Laravel 5
			$configPath = realpath(__DIR__ . '/../config/config.php');
			$this->publishes([$configPath => config_path('elasticsearch.php')]);
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		$this->app->singleton('elasticsearch', function ($app) {

			$config = $app['config']['elasticsearch'] ?: $app['config']['elasticsearch::config'];

			return new Client($config);
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
