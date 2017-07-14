<?php namespace Cviebrock\LaravelElasticsearch;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;


/**
 * Class ServiceProvider
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class LumenServiceProvider extends BaseServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
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
            return new LumenManager($app, $app['elasticsearch.factory']);
        });
        
        $app->alias('elasticsearch', LumenManager::class);

        $this->withFacades();
    }

    protected function withFacades()
    {
        class_alias('\Cviebrock\LaravelElasticsearch\Facade', 'Elasticsearch');
    }
}
