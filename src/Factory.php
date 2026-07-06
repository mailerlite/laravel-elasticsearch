<?php

namespace MailerLite\LaravelElasticsearch;

use Aws\Credentials\Credentials;
use Aws\Signature\SignatureV4;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class Factory
{
    /**
     * Make the Elasticsearch client for the given named configuration, or
     * the default client.
     *
     * @param array $config
     *
     * @return \Elastic\Elasticsearch\Client
     *
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
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
     * @return \Elastic\Elasticsearch\Client
     *
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     */
    protected function buildClient(array $config): Client
    {
        $clientBuilder = ClientBuilder::create();

        // Configure hosts
        $clientBuilder->setHosts($this->buildHosts($config['hosts']));

        // Configure authentication
        $this->configureAuthentication($clientBuilder, $config['hosts']);

        // Configure logging
        $this->configureLogging($clientBuilder, $config);

        // Configure SSL verification / CA bundle
        $ssl = Arr::get($config, 'sslVerification');
        if (is_string($ssl)) {
            $clientBuilder->setCABundle($ssl);
        } elseif (is_bool($ssl)) {
            $clientBuilder->setSSLVerification($ssl);
        }

        // Configure retries
        $retries = Arr::get($config, 'retries');
        if ($retries !== null) {
            $clientBuilder->setRetries((int) $retries);
        }

        // Configure the AWS signing handler for any AWS hosts
        $this->configureAwsHandler($clientBuilder, $config['hosts']);

        return $clientBuilder->build();
    }

    /**
     * Build the array of host strings (e.g. "https://localhost:9200") that the
     * 8.x client expects from the package's extended host configuration.
     *
     * @param array $hosts
     *
     * @return array
     */
    protected function buildHosts(array $hosts): array
    {
        $result = [];

        foreach ($hosts as $host) {
            // Allow the simple "inline" string configuration.
            if (is_string($host)) {
                $result[] = $host;

                continue;
            }

            $hostname = $host['host'] ?? 'localhost';
            $scheme = $host['scheme'] ?? null;

            if (empty($scheme)) {
                $scheme = !empty($host['aws']) ? 'https' : 'http';
            }

            $url = $scheme . '://' . $hostname;

            if (! empty($host['port'])) {
                $url .= ':' . $host['port'];
            }

            $result[] = $url;
        }

        return $result;
    }

    /**
     * Configure basic auth or API key authentication based on the first host
     * that provides credentials.
     *
     * @param \Elastic\Elasticsearch\ClientBuilder $clientBuilder
     * @param array                                $hosts
     *
     * @return void
     */
    protected function configureAuthentication(ClientBuilder $clientBuilder, array $hosts): void
    {
        foreach ($hosts as $host) {
            if (! is_array($host)) {
                continue;
            }

            if (! empty($host['api_key'])) {
                $clientBuilder->setApiKey($host['api_key'], $host['api_id'] ?? null);

                return;
            }

            if (! empty($host['user']) && ! empty($host['pass'])) {
                $clientBuilder->setBasicAuthentication($host['user'], $host['pass']);

                return;
            }
        }
    }

    /**
     * Configure the logger on the client builder.
     *
     * @param \Elastic\Elasticsearch\ClientBuilder $clientBuilder
     * @param array                                $config
     *
     * @return void
     */
    protected function configureLogging(ClientBuilder $clientBuilder, array $config): void
    {
        if (! Arr::get($config, 'logging')) {
            return;
        }

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

    /**
     * Configure a PSR-18 (Guzzle) HTTP client that signs every request with
     * AWS Signature V4 when any host is flagged as an AWS host.
     *
     * @param \Elastic\Elasticsearch\ClientBuilder $clientBuilder
     * @param array                                $hosts
     *
     * @return void
     */
    protected function configureAwsHandler(ClientBuilder $clientBuilder, array $hosts): void
    {
        foreach ($hosts as $host) {
            if (! is_array($host) || empty($host['aws'])) {
                continue;
            }

            $stack = HandlerStack::create();

            $stack->push(Middleware::mapRequest(function (RequestInterface $request) use ($host) {
                return $this->signRequest($request, $host);
            }));

            $clientBuilder->setHttpClient(new GuzzleClient(['handler' => $stack]));

            return;
        }
    }

    /**
     * Sign a PSR-7 request with AWS Signature V4 for the given host.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $host
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function signRequest(RequestInterface $request, array $host): RequestInterface
    {
        $signer = new SignatureV4('es', $host['aws_region']);

        return $signer->signRequest($request, $this->resolveAwsCredentials($host));
    }

    /**
     * Resolve the AWS credentials from the host configuration, supporting a
     * static set of keys, a Credentials instance, a callable provider or a
     * closure.
     *
     * @param array $host
     *
     * @return \Aws\Credentials\Credentials
     */
    protected function resolveAwsCredentials(array $host): Credentials
    {
        // Use the credentials as provided in the config if they are a Credentials instance.
        if (! empty($host['aws_credentials']) && $host['aws_credentials'] instanceof Credentials) {
            return $host['aws_credentials'];
        }

        // If the aws_credentials is an array try using it as a static method of the class.
        if (
            ! empty($host['aws_credentials'])
            && is_array($host['aws_credentials'])
            && Reflector::isCallable($host['aws_credentials'], true)
        ) {
            $host['aws_credentials'] = call_user_func([$host['aws_credentials'][0], $host['aws_credentials'][1]]);
        }

        // If it contains a closure you can obtain the credentials by invoking it.
        if (! empty($host['aws_credentials']) && $host['aws_credentials'] instanceof \Closure) {
            return $host['aws_credentials']()->wait();
        }

        // Fall back to the credentials from the environment / config.
        return new Credentials(
            $host['aws_key'],
            $host['aws_secret'],
            $host['aws_session_token'] ?? null
        );
    }
}
