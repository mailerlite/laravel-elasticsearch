<?php

namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


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
        'namespaces'         => 'registerNamespace',
    ];

    /**
     * Make the Elasticsearch client for the given named configuration, or
     * the default client.
     *
     * @param array $config
     *
     * @return \Elasticsearch\Client
     */
    public function make(array $config): Client
    {
        return $this->buildClient($config);
    }

    /**
     * Build and configure an Elasticsearch client.
     *
     * @param array $config
     *
     * @return \Elasticsearch\Client
     */
    protected function buildClient(array $config): Client
    {
        $clientBuilder = ClientBuilder::create();

        // Configure hosts
        $clientBuilder->setHosts($config['hosts']);

        // Configure logging
        if (Arr::get($config, 'logging')) {
            $logObject = Arr::get($config, 'logObject');
            $logPath = Arr::get($config, 'logPath');
            $logLevel = Arr::get($config, 'logLevel');
            if ($logObject && $logObject instanceof LoggerInterface) {
                $clientBuilder->setLogger($logObject);
            } elseif ($logPath && $logLevel) {
                $handler = new StreamHandler($logPath, $logLevel);
                $logObject = new Logger('log');
                $logObject->pushHandler($handler);
                $clientBuilder->setLogger($logObject);
            }
        }

        // Configure tracer
        if ($tracer = Arr::get($config, 'tracer')) {
            $clientBuilder->setTracer(app($tracer));
        }

        // Set additional client configuration
        foreach ($this->configMappings as $key => $method) {
            $value = Arr::get($config, $key);
            if (is_array($value)) {
                foreach ($value as $vItem) {
                    $clientBuilder->$method($vItem);
                }
            } elseif ($value !== null) {
                $clientBuilder->$method($value);
            }
        }

        // Configure handlers for any AWS hosts
        foreach ($config['hosts'] as $host) {
            if (isset($host['aws']) && $host['aws']) {
                $clientBuilder->setHandler(function (array $request) use ($host) {
                    $psr7Handler = \Aws\default_http_handler();
                    $signer = new \Aws\Signature\SignatureV4('es', $host['aws_region']);
                    $request['headers']['Host'][0] = parse_url($request['headers']['Host'][0])['host'] ?? $request['headers']['Host'][0];

                    // Create a PSR-7 request from the array passed to the handler
                    $psr7Request = new Request(
                        $request['http_method'],
                        (new Uri($request['uri']))
                            ->withScheme($request['scheme'])
                            ->withPort($host['port'])
                            ->withHost($request['headers']['Host'][0]),
                        $request['headers'],
                        $request['body']
                    );

                    // Create the Credentials instance with the credentials from the environment
                    $credentials = new \Aws\Credentials\Credentials(
                        $host['aws_key'],
                        $host['aws_secret'],
                        $host['aws_session_token'] ?? null
                    );
                    // check if the aws_credentials from config is set and if it contains a Credentials instance
                    if (!empty($host['aws_credentials']) && $host['aws_credentials'] instanceof \Aws\Credentials\Credentials) {
                        // Set the credentials as in config
                        $credentials = $host['aws_credentials'];
                    }

                    // If the aws_credentials is an array try using it as a static method of the class
                    if (
                        !empty($host['aws_credentials'])
                        && is_array($host['aws_credentials'])
                        && Reflector::isCallable($host['aws_credentials'], true)
                    ) {
                        $host['aws_credentials'] = call_user_func([$host['aws_credentials'][0], $host['aws_credentials'][1]]);
                    }

                    if (!empty($host['aws_credentials']) && $host['aws_credentials'] instanceof \Closure) {
                        // If it contains a closure you can obtain the credentials by invoking it
                        $credentials = $host['aws_credentials']()->wait();
                    }

                    // Sign the PSR-7 request
                    $signedRequest = $signer->signRequest(
                        $psr7Request,
                        $credentials
                    );

                    // Get curl stats
                    $http_stats = new class
                    {
                        public $data = [];
                        public function __invoke(...$args)
                        {
                            $this->data = $args[0];
                        }
                    };

                    // Send the signed request to Amazon ES
                    $response = $psr7Handler($signedRequest, ['http_stats_receiver' => $http_stats])
                        ->then(function (ResponseInterface $response) {
                            return $response;
                        }, function ($error) {
                            return $error['response'];
                        })
                        ->wait();

                    // Convert the PSR-7 response to a RingPHP response
                    return new CompletedFutureArray([
                        'status'         => $response->getStatusCode(),
                        'headers'        => $response->getHeaders(),
                        'body'           => $response->getBody()->detach(),
                        'transfer_stats' => [
                            'total_time'   => $http_stats->data['total_time'] ?? 0,
                            'primary_port' => $http_stats->data['primary_port'] ?? '',
                        ],
                        'effective_url'  => (string) $psr7Request->getUri(),
                    ]);
                });
            }
        }

        // Build and return the client
        if (
            !empty($host['api_id']) && $host['api_id'] !== null &&
            !empty($host['api_key']) && $host['api_key'] !== null
        ) {
            $clientBuilder->setApiKey($host['api_id'], $host['api_key']);
        }

        return $clientBuilder->build();
    }
}
