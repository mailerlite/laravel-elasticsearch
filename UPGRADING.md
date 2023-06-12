# Upgrading

## 9.0.2 to 9.0.4

The api_key and api_id keys have been added to the hosts section in the config. If you have an existing published config file, add them to your config:

```php
//    'connections' => [
//        'connectionName' => [
//            'hosts' => [
                'api_id' => env('ELASTICSEARCH_API_ID', null),
                'api_key' => env('ELASTICSEARCH_API_KEY', null),
//            ],
//        ],
//    ],
```
