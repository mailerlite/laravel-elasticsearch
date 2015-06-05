# Laravel-Elasticsearch

An easy way to use the official Elastic Search client in your Laravel applications.


## Installation and Configuration

1. Install the `cviebrock/laravel-elasticsearch` package via composer:

    ```shell
    $ composer require cviebrock/laravel-elasticsearch
    ```
    
2. Publish the configuration file.  For Laravel 5:

    ```shell
    php artisan vendor:publish cviebrock/laravel-elasticsearch
    ```

    In order to make this pacakge also work with Laravel 4, we can't do the
    standard configuration publishing like most Laravel 4 packages do.  You will
    need to simply copy the configuration file into your application's configuration folder:
    
    ```shell
    cp vendor/cviebrock/laravel-elasticsearch/config/elasticsearch.php app/config/
    ```

3. Add the service provider and facade (`config/app.php` for Laravel 5 or `app/config/app.php` for Laravel 4):

    ```php
    'providers' => array(
        ...
        'Cviebrock\LaravelElasticSearch\ServiceProvider',
    )

    'aliases' => array(
        ...
        'Elasticsearch' => 'Cviebrock\LaravelElasticSearch\Facade',
    )
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

