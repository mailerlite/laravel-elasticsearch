<?php namespace Cviebrock\LaravelElasticsearch;

use Illuminate\Support\Facades\Facade as BaseFacade;


/**
 * Class Facade
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class Facade extends BaseFacade {

    /**
     * @param $name
     * @return mixed
     */
    public static function connection($name) {

        return app('Cviebrock\LaravelElasticsearch\Factory')->make($name);
    }

	/**
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'elasticsearch';
	}
}
