<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Arr;
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
                $clientBuilder->setHandler(function(array $request) use ($host) {
                    $psr7Handler = \Aws\default_http_handler();
                    $signer = new \Aws\Signature\SignatureV4('es', $host['aws_region']);
                    $request['headers']['Host'][0] = parse_url($request['headers']['Host'][0])['host'];

                    // Create a PSR-7 request from the array passed to the handler
                    $psr7Request = new \GuzzleHttp\Psr7\Request(
                        $request['http_method'],
                        (new \GuzzleHttp\Psr7\Uri($request['uri']))
                            ->withScheme($request['scheme'])
                            ->withHost($request['headers']['Host'][0]),
                        $request['headers'],
                        $request['body']
                    );

                    // Sign the PSR-7 request with credentials from the environment
                    $signedRequest = $signer->signRequest(
                        $psr7Request,
                        new \Aws\Credentials\Credentials($host['aws_key'], $host['aws_secret'])
                    );

                    // Send the signed request to Amazon ES
                    /** @var \Psr\Http\Message\ResponseInterface $response */
                    $response = $psr7Handler($signedRequest)
                        ->then(function(\Psr\Http\Message\ResponseInterface $response) {
                            return $response;
                        }, function($error) {
                            return $error['response'];
                        })
                        ->wait();

                    // Convert the PSR-7 response to a RingPHP response
                    return new \GuzzleHttp\Ring\Future\CompletedFutureArray([
                        'status'         => $response->getStatusCode(),
                        'headers'        => $response->getHeaders(),
                        'body'           => $response->getBody()
                                                     ->detach(),
                        'transfer_stats' => ['total_time' => 0],
                        'effective_url'  => (string)$psr7Request->getUri(),
                    ]);
                });
            }
        }

        // Build and return the client

        return $clientBuilder->build();
    }
}
