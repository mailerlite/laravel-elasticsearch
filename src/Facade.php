<?php namespace MailerLite\LaravelElasticsearch;

use Illuminate\Support\Facades\Facade as BaseFacade;


/**
 * Class Facade
 *
 * @package MailerLite\LaravelElasticsearch
 */
class Facade extends BaseFacade
{

    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'elasticsearch';
    }
}
