# Laravel-Elasticsearch

An easy way to use the official Elastic Search client in your Laravel 5 or Lumen applications.

[![Build Status](https://travis-ci.org/cviebrock/laravel-elasticsearch.svg)](https://travis-ci.org/cviebrock/laravel-elasticsearch)
[![Total Downloads](https://poser.pugx.org/cviebrock/laravel-elasticsearch/downloads.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch/v/stable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch/v/unstable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cviebrock/laravel-elasticsearch/badges/quality-score.png?format=flat)](https://scrutinizer-ci.com/g/cviebrock/laravel-elasticsearch)

* [Installation and Configuration](#installation-and-configuration)
* [Usage](#usage)
* [Bugs, Suggestions and Contributions](#bugs-suggestions-and-contributions)
* [Copyright and License](#copyright-and-license)



## Installation and Configuration

Install the `cviebrock/laravel-elasticsearch` package via composer:

```shell
composer require cviebrock/laravel-elasticsearch
```

### Laravel 

Add the service provider and facade to `config/app.php`:

```php
'providers' => [
    ...
    Cviebrock\LaravelElasticsearch\ServiceProvider::class,   
]

'aliases' => [
    ...
    'Elasticsearch' => Cviebrock\LaravelElasticsearch\Facade::class,
]
```
    
Publish the configuration file:

```shell
php artisan vendor:publish --provider="Cviebrock\LaravelElasticsearch\ServiceProvider"
```

### Lumen

If you work with Lumen, please register the LumenServiceProvider in `bootstrap/app.php`:

```php
$app->register(Cviebrock\LaravelElasticsearch\LumenServiceProvider::class);
```

And manually copy the configuration file to your application.

**Note:** don't forget to register your elasticsearch.php config in bootstrap/app.php
```php
$app->configure('elasticsearch');
```



## Usage

The `Elasticsearch` facade is just an entry point into the ES client, so previously
you might have used:

```php
$data = [
    'body' => [
        'testField' => 'abc'
    ],
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
];

$client = ClientBuilder::create()->build();
$return = $client->index($data);
```

You can now replace those last two lines with simply:

```php
$return = Elasticsearch::index($data);
```

That will run the command on the default connection.  You can run a command on
any connection (see the `defaultConnection` setting and `connections` array in
the configuration file).

```php
$return = Elasticsearch::connection('connectionName')->index($data);
```

Please be noticed that you should not use Facade in Lumen. 
So, in Lumen - you should use IoC or get the ElasticSearch service object from the application.
```php
$elasticSearch = $this->app('elasticsearch');
```


## Bugs, Suggestions and Contributions

Thanks to [everyone](https://github.com/cviebrock/laravel-elasticsearch/graphs/contributors)
who has contributed to this project!

Please use [Github](https://github.com/cviebrock/laravel-elasticsearch) for reporting bugs, 
and making comments or suggestions.
 
See [CONTRIBUTING.md](CONTRIBUTING.md) for how to contribute changes.



## Copyright and License

[laravel-elasticsearch](https://github.com/cviebrock/laravel-elasticsearch)
was written by [Colin Viebrock](http://viebrock.ca) and is released under the 
[MIT License](LICENSE.md).

Copyright (c) 2015 Colin Viebrock
