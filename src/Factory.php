<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;


class Factory
{

    /**
     * Map configuration array keys with ES ClientBuilder setters
     *
     * @var array
     */
    protected $configMappings = [
        'sslVerification'    => 'setSSLVerification',
        'sniffOnStart'       => 'setSniffOnStart',
        'retries'            => 'setRetries',
        'httpHandler'        => 'setHandler',
        'connectionPool'     => 'setConnectionPool',
        'connectionSelector' => 'setSelector',
        'serializer'         => 'setSerializer',
        'connectionFactory'  => 'setConnectionFactory',
        'endpoint'           => 'setEndpoint',
    ];

    /**
     * Make the Elasticsearch client for the given named configuration, or
     * the default client.
     *
     * @param array $config
     * @return \Elasticsearch\Client|mixed
     */
    public function make(array $config)
    {
        // Build the client
        return $this->buildClient($config);
    }

    /**
     * Build and configure an Elasticsearch client.
     *
     * @param array $config
     * @return \Elasticsearch\Client
     */
    protected function buildClient(array $config)
    {

        $clientBuilder = ClientBuilder::create();

        // Configure hosts

        $clientBuilder->setHosts($config['hosts']);

        // Configure logging

        if (array_get($config, 'logging')) {
            $logObject = array_get($config, 'logObject');
            $logPath = array_get($config, 'logPath');
            $logLevel = array_get($config, 'logLevel');
            if ($logObject && $logObject instanceof LoggerInterface) {
                $clientBuilder->setLogger($logObject);
            } else if ($logPath && $logLevel) {
                $logObject = ClientBuilder::defaultLogger($logPath, $logLevel);
                $clientBuilder->setLogger($logObject);
            }
        }

        // Set additional client configuration

        foreach ($this->configMappings as $key => $method) {
            $value = array_get($config, $key);
            if ($value !== null) {
                call_user_func([$clientBuilder, $method], $value);
            }
        }

        // Build and return the client

        return $clientBuilder->build();
    }
}
