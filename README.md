# Laravel-Elasticsearch

An easy way to use the official Elastic Search client in your Laravel applications.


## Installation and Configuration

1. Install the `cviebrock/laravel-elasticsearch` package via composer:

    ```shell
    $ composer require cviebrock/laravel-elasticsearch
    ```
    
2. Publish the configuration file.  For Laravel 4:

    ```shell
    php artisan config:publish cviebrock/laravel-elasticsearch
    ```

    Or for Laravel 5:

    ```shell
    php artisan vendor:publish cviebrock/laravel-elasticsearch
    ```

3. Add the service provider (`app/config/app.php` for Laravel 4, `config/app.php` for Laravel 5):

    ```php
    # Add the service provider to the `providers` array
    'providers' => array(
        ...
        'Cviebrock\LaravelElasticSearch\ServiceProvider',
    )

    # Add the facade to the `aliases` array
    'aliases' => array(
        ...
        'Elasticsearch' => 'Cviebrock\LaravelElasticSearch\Facade',
    )
    ```



## Usage

The `Elasticsearch` facade is just an entry point into the ES client.

```php
$data = [
    'body' => [
        'testField' => 'abc'
    ],
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
];
$return = Elasticsearch::index($data);
```
