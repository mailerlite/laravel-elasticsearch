<?php namespace Cviebrock\LaravelElasticsearch;

use Illuminate\Support\Facades\Facade as BaseFacade;


/**
 * Class Facade
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class Facade extends BaseFacade {

	/**
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'elasticsearch';
	}
}
