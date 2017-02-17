<?php namespace Cviebrock\LaravelElasticsearch;

use Illuminate\Support\Facades\Facade as BaseFacade;


/**
 * Class Facade
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class Facade extends BaseFacade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'elasticsearch';
    }
}
