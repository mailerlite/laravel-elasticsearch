<?php

return [

	/**
	 * Hosts
	 *
	 * This is an array of hosts that the client will connect to. It can be a
	 * single host name, or an array if you are running a cluster of
	 * Elasticsearch instances.
	 *
	 * This is the only configuration value that is mandatory.
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_host_configuration
	 */

	'hosts' => [
		'localhost:9200'
	],


	/**
	 * SSL
	 *
	 * If your ES instance uses an out-dated or self-signed SSL certificate,
	 * you will need to pass in the certificate bundle.  This can either be the path
	 * to the certificate file (for self-signed certs), or a package like
	 * https://github.com/Kdyby/CurlCaBundle.  See the documentation below for all the details.
	 *
	 * If you are using SSL instances, and the certificates are up-to-date and signed
	 * by a public certificate authority, then you can leave this null and just use "https"
	 * in the host path(s) above and you should be fine.
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_security.html#_ssl_encryption_2
	 */

	'sslVerification' => null,


	/**
	 * Logging
	 *
	 * Logging is handled by passing in an instance of Monolog\Logger (which is
	 * what Laravel's logger is by default).
	 *
	 * If logging is enabled, you either need to set the path and log level (some
	 * defaults are given for you below), or you can use a custom logger by
	 * setting 'logObject' to an instance of Psr\Log\LoggerInterface.
	 *
	 * (Note: logObject takes precedent over logPath/logLevel, so leave logObject
	 * null if you just want regular old logging.)
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#enabling_logger
	 */

	'logging' => false,

	'logPath' => storage_path('/logs/elasticsearch.log'),

	'logLevel' => Monolog\Logger::INFO,

	'logObject' => null,


	/**
	 * Retries
	 *
	 * By default, the client will retry n times, where n = number of nodes in your cluster.
	 * If you would like to disable retries, or change the number, you can do so here.
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_set_retries
	 */

	'retries' => null,


	/**
	 * The remainder of the configuration options can almost always be left as-is unless you have
	 * specific reasons to change them.  Refer to the appropriate sections in the Elasticsearch
	 * documentation for what each option does and what values it expects.
	 */

	/**
	 * HTTP Handler
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_configure_the_http_handler
	 * @see http://ringphp.readthedocs.org/en/latest/client_handlers.html
	 */

	'httpHandler' => null,


	/**
	 * Connection Pool
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_connection_pool
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_connection_pool.html
	 */

	'connectionPool' => null,


	/**
	 * Connection Selector
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_connection_selector
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_selectors.html
	 */

	'connectionSelector' => null,


	/**
	 * Serializer
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_the_serializer
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_serializers.html
	 */

	'serializer' => null,


	/**
	 * Connection Factory
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_setting_a_custom_connectionfactory
	 */

	'connectionFactory' => null,

	/**
	 * Endpoint
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_set_the_endpoint_closure
	 */

	'endpoint' => null,

];
