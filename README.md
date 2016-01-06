# Laravel-Elasticsearch

An easy way to use the official Elastic Search client in your Laravel applications.

[![Build Status](https://travis-ci.org/cviebrock/laravel-elasticsearch.svg)](https://travis-ci.org/cviebrock/laravel-elasticsearch)
[![Total Downloads](https://poser.pugx.org/cviebrock/laravel-elasticsearch/downloads.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch/v/stable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch/v/unstable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)

* [Installation and Configuration](#installation)
* [Usage](#usage)
* [Bugs, Suggestions and Contributions](#bugs)
* [Copyright and License](#copyright)


<a name="installation"></a>
## Installation and Configuration

Install the `cviebrock/laravel-elasticsearch` package via composer:

```shell
composer require cviebrock/laravel-elasticsearch
```
    
Publish the configuration file.  For Laravel 5:

```shell
php artisan vendor:publish --provider="Cviebrock\LaravelElasticsearch\ServiceProvider"
```

In order to make this package also work with Laravel 4, we can't do the
standard configuration publishing like most Laravel 4 packages do.  You will
need to simply copy the configuration file into your application's configuration folder:
    
```shell
cp vendor/cviebrock/laravel-elasticsearch/config/elasticsearch.php app/config/
```

Add the service provider and facade (`config/app.php` for Laravel 5 or `app/config/app.php` for Laravel 4):

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

<a name="usage"></a>
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



<a name="bugs"></a>
## Bugs, Suggestions and Contributions

Thanks to [everyone](/cviebrock/laravel-elasticsearch/graphs/contributors) who has contributed 
to this project!

Please use Github for bugs, comments, suggestions.

1. Fork the project.
2. Create your bugfix/feature branch and write your (well-commented) code.
3. Create unit tests for your code:
	- Run `composer install --dev` in the root directory to install required testing packages.
	- Add your test methods to `laravel-elasticsearch/tests/`.
	- Run `vendor/bin/phpunit` to the new (and all previous) tests and make sure everything passes.
3. Commit your changes (and your tests) and push to your branch.
4. Create a new pull request against the `master` branch.


<a name="copyright"></a>
## Copyright and License

Laravel-Elasticsearch was written by Colin Viebrock and released under the MIT License. 
See the LICENSE file for details.

Copyright 2015 Colin Viebrock
