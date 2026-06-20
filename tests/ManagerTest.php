<?php

declare(strict_types=1);

namespace MailerLite\LaravelElasticsearch\Tests;

use Elastic\Elasticsearch\Client;
use InvalidArgumentException;
use MailerLite\LaravelElasticsearch\Factory;
use MailerLite\LaravelElasticsearch\Manager;

final class ManagerTest extends TestCase
{
    private function manager(): Manager
    {
        return new Manager($this->app, new Factory());
    }

    public function testConnectionReturnsClient(): void
    {
        $this->assertInstanceOf(Client::class, $this->manager()->connection());
    }

    public function testConnectionsAreCached(): void
    {
        $manager = $this->manager();

        $this->assertSame($manager->connection(), $manager->connection());
    }

    public function testDefaultConnectionDefaultsToDefault(): void
    {
        $this->assertSame('default', $this->manager()->getDefaultConnection());
    }

    public function testSetDefaultConnection(): void
    {
        $manager = $this->manager();
        $manager->setDefaultConnection('another');

        $this->assertSame('another', $manager->getDefaultConnection());
    }

    public function testUnknownConnectionThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Elasticsearch connection [does-not-exist] not configured.');

        $this->manager()->connection('does-not-exist');
    }

    public function testGetConnectionsTracksBuiltConnections(): void
    {
        $manager = $this->manager();

        $this->assertSame([], $manager->getConnections());

        $manager->connection();

        $this->assertArrayHasKey('default', $manager->getConnections());
    }
}
