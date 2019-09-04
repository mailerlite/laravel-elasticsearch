# Laravel-Elasticsearch

An easy way to use the [official Elastic Search client](https://github.com/elastic/elasticsearch-php) in your Laravel or Lumen applications.

[![Build Status](https://travis-ci.org/cviebrock/laravel-elasticsearch.svg)](https://travis-ci.org/cviebrock/laravel-elasticsearch)
[![Total Downloads](https://poser.pugx.org/cviebrock/laravel-elasticsearch/downloads.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch/v/stable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch/v/unstable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cviebrock/laravel-elasticsearch/badges/quality-score.png?format=flat)](https://scrutinizer-ci.com/g/cviebrock/laravel-elasticsearch)

* [Installation and Configuration](#installation-and-configuration)
* [Usage](#usage)
* [Advanced Usage](#advanced-usage)
* [Bugs, Suggestions, Contributions and Support](#bugs-suggestions-contributions-and-support)
* [Copyright and License](#copyright-and-license)



## Installation and Configuration

Install the current version of the `cviebrock/laravel-elasticsearch` package via composer:

```sh
composer require cviebrock/laravel-elasticsearch
```

If you are using ElasticSearch version 5, then install version 2 of this package:

```sh
composer require cviebrock/laravel-elasticsearch:^2
```

### Laravel

The package's service provider will automatically register its service provider.

Publish the configuration file:

```sh
php artisan vendor:publish --provider="Cviebrock\LaravelElasticsearch\ServiceProvider"
```

##### Alternative configuration method via .env file

After you publish the configuration file as suggested above, you may configure ElasticSearch
by adding the following to your application's `.env` file (with appropriate values):
  
```ini
ELASTICSEARCH_HOST=localhost
ELASTICSEARCH_PORT=9200
ELASTICSEARCH_SCHEME=http
ELASTICSEARCH_USER=
ELASTICSEARCH_PASS=
```

##### Connecting to AWS Elasticsearch Service

If you are connecting to ElasticSearch instances on Amazon AWS, then you'll also
need to `composer require aws/aws-sdk-php:^3.80` and add the following to your 
`.env` file:

```ini
AWS_ELASTICSEARCH_ENABLED=true
AWS_REGION=...
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
```  

### Lumen

If you work with Lumen, please register the service provider and configuration in `bootstrap/app.php`:

```php
$app->register(Cviebrock\LaravelElasticsearch\ServiceProvider::class);
$app->configure('elasticsearch');
```

Manually copy the configuration file to your application.



## Usage

The `Elasticsearch` facade is just an entry point into the [ES client](https://github.com/elastic/elasticsearch-php),
so previously you might have used:

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

Lumen users who wish to use Facades can do so by editing the 
`bootstrap/app.php` file to include the following:

```php
$app->withFacades(true, [
    ...
    Cviebrock\LaravelElasticsearch\Facade::class => 'Elasticsearch',
    ...
]);
```

Lumen users who aren't using facades will need to use dependency injection 
or the application container in order to get the ES service object:

```php
// using injection:
public function handle(\Cviebrock\LaravelElasticsearch\Manager $elasticsearch)
{
    $elasticsearch->ping();
}

// using application container:
$elasticSearch = $this->app('elasticsearch');
```

Of course, dependency injection and the application container work 
for Laravel applications as well.



## Advanced Usage

Because the package is a wrapper around the official Elastic client, you can 
do pretty much anything with this package.  Not only can you perform standard 
CRUD operations, but you can monitor the health of your Elastic cluster programmatically, 
back it up, or make changes to it.  Some of these operations are done through 
"namespaced" commands, which this package happily supports.

To grab statistics for an index:

```php
$stats = Elasticsearch::indices()->stats(['index' => 'my_index']);
$stats = Elasticsearch::nodes()->stats();
$stats = Elasticsearch::cluster()->stats();
```

To create and restore snapshots (read the Elastic docs about creating repository paths and plugins first):

```php
$response = Elasticsearch::snapshots()->create($params);
$response = Elasticsearch::snapshots()->restore($params);
```

To delete whole indices (be careful!):

```php
$response = Elasticsearch::indices()->delete(['index' => 'my_index']);
```

Please remember that this package is a thin wrapper around a large number of very 
sophisticated and well-documented Elastic features.  Information about those features 
and the methods and parameters used to call them can be found in the 
[Elastic documentation](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/index.html).
Help with using them is available via the [Elastic forums](https://discuss.elastic.co/) 
and on sites like [Stack Overflow](https://stackoverflow.com/questions/tagged/elasticsearch).



## Bugs, Suggestions, Contributions and Support

Thanks to [everyone](https://github.com/cviebrock/laravel-elasticsearch/graphs/contributors)
who has contributed to this project!

Special thanks to 
[JetBrains](https://www.jetbrains.com/?from=cviebrock/laravel-elasticsearch) for their 
Open Source License Program ... and the excellent PHPStorm IDE, of course!

[![JetBrains](./.github/jetbrains.svg)](https://www.jetbrains.com/?from=cviebrock/laravel-elasticsearch)

Please use [Github](https://github.com/cviebrock/laravel-elasticsearch) for reporting bugs, 
and making comments or suggestions.
 
See [CONTRIBUTING.md](CONTRIBUTING.md) for how to contribute changes.



## Copyright and License

[laravel-elasticsearch](https://github.com/cviebrock/laravel-elasticsearch)
was written by [Colin Viebrock](http://viebrock.ca) and is released under the 
[MIT License](LICENSE.md).

Copyright (c) 2015 Colin Viebrock
