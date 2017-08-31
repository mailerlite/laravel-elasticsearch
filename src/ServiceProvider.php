<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\Client;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;


/**
 * Class ServiceProvider
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class ServiceProvider extends BaseServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setUpConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton('elasticsearch.factory', function($app) {
            return new Factory();
        });

        $app->singleton('elasticsearch', function($app) {
            return new Manager($app, $app['elasticsearch.factory']);
        });

        $app->singleton(Client::class, function($app) {
            return $app['elasticsearch']->connection();
        });

        if ($app instanceof LumenApplication) {
            $this->withLumenFacades();
        }
    }

    protected function setUpConfig()
    {
        $source = dirname(__DIR__) . '/config/elasticsearch.php';

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('elasticsearch.php')], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('elasticsearch');
        }

        $this->mergeConfigFrom($source, 'elasticsearch');
    }

    protected function withLumenFacades()
    {
        class_alias(Facade::class, 'Elasticsearch');
    }
}
