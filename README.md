# Laravel-Elasticsearch

An easy way to use the official Elastic Search client in your Laravel applications!


## Installation

Install the `cviebrock/laravel-elasticsearch` package via composer:

```shell
$ composer require cviebrock/laravel-elasticsearch
```
    
## Configuration

### Laravel 4

Copy the base configuration file into your app:


### Laravel 5

Publish the configuration file:

```shell
php artisan config:publish cviebrock/laravel-elasticsearch
```

Add the service provider to `config/app.php`:

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
