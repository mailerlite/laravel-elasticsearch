<?php

declare(strict_types=1);

namespace MailerLite\LaravelElasticsearch\Tests;

use Aws\Credentials\Credentials;
use Elastic\Elasticsearch\Client;
use GuzzleHttp\Psr7\Request;
use MailerLite\LaravelElasticsearch\Factory;
use Psr\Log\NullLogger;
use ReflectionMethod;

final class FactoryTest extends TestCase
{
    /**
     * @param array $config
     *
     * @return \Elastic\Elasticsearch\Client
     */
    private function make(array $config): Client
    {
        return (new Factory())->make($config);
    }

    /**
     * @return mixed
     */
    private function callProtected(Factory $factory, string $method, array $args)
    {
        $reflection = new ReflectionMethod(Factory::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($factory, $args);
    }

    public function testMakeReturnsClientForMinimalConfig(): void
    {
        $client = $this->make([
            'hosts' => [
                ['host' => 'localhost', 'port' => 9200],
            ],
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testBuildHostsFromExtendedConfiguration(): void
    {
        $hosts = $this->callProtected(new Factory(), 'buildHosts', [[
            ['host' => 'localhost', 'port' => 9200],
        ]]);

        $this->assertSame(['http://localhost:9200'], $hosts);
    }

    public function testBuildHostsRespectsExplicitScheme(): void
    {
        $hosts = $this->callProtected(new Factory(), 'buildHosts', [[
            ['host' => 'example.com', 'port' => 443, 'scheme' => 'https'],
        ]]);

        $this->assertSame(['https://example.com:443'], $hosts);
    }

    public function testBuildHostsDefaultsToHttpsForAwsHosts(): void
    {
        $hosts = $this->callProtected(new Factory(), 'buildHosts', [[
            ['host' => 'search-foo.es.amazonaws.com', 'aws' => true],
        ]]);

        $this->assertSame(['https://search-foo.es.amazonaws.com'], $hosts);
    }

    public function testBuildHostsOmitsEmptyPort(): void
    {
        $hosts = $this->callProtected(new Factory(), 'buildHosts', [[
            ['host' => 'localhost', 'port' => null],
        ]]);

        $this->assertSame(['http://localhost'], $hosts);
    }

    public function testBuildHostsAcceptsInlineStrings(): void
    {
        $hosts = $this->callProtected(new Factory(), 'buildHosts', [[
            'https://localhost:9200',
        ]]);

        $this->assertSame(['https://localhost:9200'], $hosts);
    }

    public function testMakeWithBasicAuthentication(): void
    {
        $client = $this->make([
            'hosts' => [
                ['host' => 'localhost', 'port' => 9200, 'user' => 'elastic', 'pass' => 'secret'],
            ],
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithApiKey(): void
    {
        $client = $this->make([
            'hosts' => [
                ['host' => 'localhost', 'port' => 9200, 'api_id' => 'id', 'api_key' => 'key'],
            ],
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithSslVerificationDisabled(): void
    {
        $client = $this->make([
            'hosts'           => [['host' => 'localhost', 'port' => 9200]],
            'sslVerification' => false,
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithRetries(): void
    {
        $client = $this->make([
            'hosts'   => [['host' => 'localhost', 'port' => 9200]],
            'retries' => 3,
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithCustomLogger(): void
    {
        $client = $this->make([
            'hosts'     => [['host' => 'localhost', 'port' => 9200]],
            'logging'   => true,
            'logObject' => new NullLogger(),
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testMakeWithAwsHostBuildsClient(): void
    {
        $client = $this->make([
            'hosts' => [
                [
                    'host'       => 'search-foo.us-east-1.es.amazonaws.com',
                    'port'       => null,
                    'aws'        => true,
                    'aws_region' => 'us-east-1',
                    'aws_key'    => 'key',
                    'aws_secret' => 'secret',
                ],
            ],
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testSignRequestAddsSigV4AuthorizationHeader(): void
    {
        $host = [
            'aws_region' => 'us-east-1',
            'aws_key'    => 'AKIDEXAMPLE',
            'aws_secret' => 'secretkey',
        ];

        $request = new Request('GET', 'https://search-foo.us-east-1.es.amazonaws.com/_cluster/health');

        $signed = $this->callProtected(new Factory(), 'signRequest', [$request, $host]);

        $authorization = $signed->getHeaderLine('Authorization');

        $this->assertStringContainsString('AWS4-HMAC-SHA256', $authorization);
        $this->assertStringContainsString('Credential=AKIDEXAMPLE', $authorization);
        $this->assertStringContainsString('us-east-1/es/aws4_request', $authorization);
    }

    public function testResolveAwsCredentialsFromStaticKeys(): void
    {
        $credentials = $this->callProtected(new Factory(), 'resolveAwsCredentials', [[
            'aws_key'           => 'key',
            'aws_secret'        => 'secret',
            'aws_session_token' => 'token',
        ]]);

        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertSame('key', $credentials->getAccessKeyId());
        $this->assertSame('secret', $credentials->getSecretKey());
        $this->assertSame('token', $credentials->getSecurityToken());
    }

    public function testResolveAwsCredentialsUsesProvidedInstance(): void
    {
        $provided = new Credentials('given-key', 'given-secret');

        $credentials = $this->callProtected(new Factory(), 'resolveAwsCredentials', [[
            'aws_key'         => 'ignored',
            'aws_secret'      => 'ignored',
            'aws_credentials' => $provided,
        ]]);

        $this->assertSame($provided, $credentials);
    }

    public function testResolveAwsCredentialsFromClosure(): void
    {
        $expected = new Credentials('closure-key', 'closure-secret');

        $credentials = $this->callProtected(new Factory(), 'resolveAwsCredentials', [[
            'aws_key'         => 'ignored',
            'aws_secret'      => 'ignored',
            'aws_credentials' => function () use ($expected) {
                return \GuzzleHttp\Promise\Create::promiseFor($expected);
            },
        ]]);

        $this->assertSame($expected, $credentials);
    }
}
