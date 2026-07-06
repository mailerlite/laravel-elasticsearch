<?php

return [

    /**
     * You can specify one of several different connections when building an
     * Elasticsearch client.
     *
     * Here you may specify which of the connections below you wish to use
     * as your default connection when building an client. Of course you may
     * use create several clients at once, each with different configurations.
     */

    'defaultConnection' => 'default',

    /**
     * These are the connection parameters used when building a client.
     */

    'connections' => [

        'default' => [

            /**
             * Hosts
             *
             * This is an array of hosts that the client will connect to. It can be a
             * single host, or an array if you are running a cluster of Elasticsearch
             * instances.
             *
             * This is the only configuration value that is mandatory.
             *
             * The 8.x client expects a list of host strings (e.g. "https://localhost:9200").
             * For convenience this package will build those strings for you from the
             * "host", "port" and "scheme" keys below. You may also pass a plain string
             * instead of the array if you prefer.
             *
             * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/connecting.html
             */

            'hosts' => [
                [
                    'host'              => env('ELASTICSEARCH_HOST', 'localhost'),
                    // For local development, the default Elasticsearch port is 9200.
                    // If you are connecting to an Elasticsearch instance on AWS, you probably want to set this to null
                    'port'              => env('ELASTICSEARCH_PORT', 9200),
                    // The scheme defaults to "http" (or "https" for AWS hosts) when left null.
                    'scheme'            => env('ELASTICSEARCH_SCHEME', null),

                    // Basic authentication
                    'user'              => env('ELASTICSEARCH_USER', null),
                    'pass'              => env('ELASTICSEARCH_PASS', null),

                    // Alternatively, you can log in via API keys
                    'api_id'            => env('ELASTICSEARCH_API_ID', null),
                    'api_key'           => env('ELASTICSEARCH_API_KEY', null),

                    // If you are connecting to an Elasticsearch instance on AWS, you will need these values as well
                    'aws'               => env('AWS_ELASTICSEARCH_ENABLED', false),
                    'aws_region'        => env('AWS_REGION', ''),
                    'aws_key'           => env('AWS_ACCESS_KEY_ID', ''),
                    'aws_secret'        => env('AWS_SECRET_ACCESS_KEY', ''),
                    'aws_credentials'   => null,
                    'aws_session_token' => env('AWS_SESSION_TOKEN', null),
                ],
            ],

            /**
             * SSL
             *
             * If your Elasticsearch instance uses an out-dated or self-signed SSL
             * certificate, you will need to pass in the path to the certificate bundle.
             *
             * Pass a string with the path to the CA bundle to verify against a custom
             * certificate, or a boolean to enable/disable SSL verification entirely.
             *
             * If you are using SSL instances, and the certificates are up-to-date and
             * signed by a public certificate authority, then you can leave this null and
             * just use "https" in the host scheme above and you should be fine.
             *
             * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/connecting.html#auth-tls
             */

            'sslVerification' => null,

            /**
             * Logging
             *
             * Logging is handled by passing in an instance of Psr\Log\LoggerInterface
             * (which coincidentally is what Laravel's default logger is).
             *
             * If logging is enabled, you either need to set the path and log level
             * (some defaults are given for you below), or you can use a custom logger by
             * setting 'logObject' to an instance of Psr\Log\LoggerInterface. In fact,
             * if you just want to use the default Laravel logger, then set 'logObject'
             * to \Log::getLogger().
             *
             * Note: 'logObject' takes precedent over 'logPath'/'logLevel', so set
             * 'logObject' null if you just want file-based logging to a custom path.
             *
             * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/logging.html
             */

            'logging' => false,

            // If you have an existing instance of a PSR-3 logger you can use it here.
            // 'logObject' => \Log::getLogger(),

            'logPath' => storage_path('logs/elasticsearch.log'),

            'logLevel' => Monolog\Logger::INFO,

            /**
             * Retries
             *
             * By default, the client will retry n times, where n = number of nodes in
             * your cluster. If you would like to disable retries, or change the number,
             * you can do so here.
             *
             * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html
             */

            'retries' => null,

        ],

    ],

];
