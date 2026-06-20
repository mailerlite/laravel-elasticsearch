# Upgrading

## 11.x to 12.x (Elasticsearch 7 to 8)

Version 12 upgrades the underlying client from `elasticsearch/elasticsearch` 7.x
to 8.x. This is the official client for Elasticsearch 8 and contains a number of
breaking changes. If you need to stay on Elasticsearch 7, keep using version 11
of this package (`composer require mailerlite/laravel-elasticsearch:^11`).

### Requirements

- PHP `8.2` or higher.
- Laravel `11`, `12` or `13` (support for Laravel `10` and earlier has been dropped). Note that Laravel `13` itself requires PHP `8.3`.
- An Elasticsearch `8.x` cluster.

### Namespace change

The client namespace changed from `Elasticsearch\` to `Elastic\Elasticsearch\`.
Update any references in your application, for example:

```diff
- use Elasticsearch\Client;
- use Elasticsearch\ClientBuilder;
+ use Elastic\Elasticsearch\Client;
+ use Elastic\Elasticsearch\ClientBuilder;
```

The injected client and the `Client::class` container binding now resolve to
`Elastic\Elasticsearch\Client`.

### Responses are objects, not arrays

The 8.x client returns `Elastic\Elasticsearch\Response\Elasticsearch` objects
instead of arrays. The object implements `ArrayAccess` so `$response['key']`
still works, but to get the previous behaviour explicitly use one of:

```php
$response->asArray();   // associative array (previous default return value)
$response->asObject();  // stdClass
$response->asString();  // raw JSON
$response->asBool();    // true on a 2xx status code
```

Endpoints that used to return a boolean (e.g. `indices()->exists()` and
`ping()`) now return a response object — call `->asBool()` on the result:

```diff
- if ($client->indices()->exists(['index' => 'my_index'])) {
+ if ($client->indices()->exists(['index' => 'my_index'])->asBool()) {
```

Also note that `4xx`/`5xx` responses now throw
`Elastic\Elasticsearch\Exception\ClientResponseException` /
`ServerResponseException` instead of returning an array.

### Configuration changes

The 8.x `ClientBuilder` removed several options. If your published
`config/elasticsearch.php` sets any of the following keys, they are now ignored
and can be removed: `sniffOnStart`, `httpHandler`, `connectionPool`,
`connectionSelector`, `serializer`, `connectionFactory`, `endpoint`,
`namespaces`, `tracer`.

Host, authentication, logging, retries and SSL configuration keep working as
before. For SSL, `sslVerification` accepts a boolean to toggle verification, or a
string path to a CA bundle.

### Elasticsearch 8 enables security by default

Unlike Elasticsearch 7, an Elasticsearch 8 cluster ships with TLS and
authentication enabled out of the box. After upgrading your cluster you will most
likely need to:

- switch the host `scheme` to `https`;
- provide credentials via `user`/`pass` (basic auth) or `api_id`/`api_key`;
- supply the CA certificate for self-signed clusters via `sslVerification` (a path
  to the CA bundle), or set it to `false` for local development only.

These are the same configuration keys as before — see the "Security and TLS"
section of the README for details.

### AWS connections

AWS request signing has been reworked to use the 8.x PSR-18 HTTP layer (a Guzzle
client with a Signature V4 middleware) instead of the old RingPHP handler. The
configuration keys are unchanged (`aws`, `aws_region`, `aws_key`, `aws_secret`,
`aws_credentials`, `aws_session_token`), so no application changes are required
beyond making sure `aws/aws-sdk-php` (`^3.80`) is installed.

### Lumen support removed

Lumen is no longer supported. This package now requires Laravel 11 or newer
(`illuminate/*` `^11`), whereas Lumen is capped at `illuminate/*` 10 and is no
longer actively maintained. If you rely on Lumen, stay on version 11 of this
package.
