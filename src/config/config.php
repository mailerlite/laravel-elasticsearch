<?php

return [

	/**
	 * Hosts
	 *
	 * This is an array of hosts that the client will connect to. It can be a
	 * single host name, or an array if you are running a cluster of Elasticsearch
	 * instances.
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
	 * If your Elasticsearch instance uses an out-dated or self-signed SSL
	 * certificate, you will need to pass in the certificate bundle.  This can
	 * either be the path to the certificate file (for self-signed certs), or a
	 * package like https://github.com/Kdyby/CurlCaBundle.  See the documentation
	 * below for all the details.
	 *
	 * If you are using SSL instances, and the certificates are up-to-date and
	 * signed by a public certificate authority, then you can leave this null and
	 * just use "https" in the host path(s) above and you should be fine.
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_security.html#_ssl_encryption_2
	 */

	'sslVerification' => null,


	/**
	 * Logging
	 *
	 * Logging is handled by passing in an instance of Monolog\Logger (which
	 * coincidentally is what Laravel's default logger is).
	 *
	 * If logging is enabled, you either need to set the path and log level
	 * (some defaults are given for you below), or you can use a custom logger by
	 * setting 'logObject' to an instance of Psr\Log\LoggerInterface.  In fact,
	 * if you just want to use the default Laravel logger, then set 'logObject'
	 * to \Log::getMonolog().
	 *
	 * Note: 'logObject' takes precedent over 'logPath'/'logLevel', so set
	 * 'logObject' null if you just want file-based logging to a custom path.
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#enabling_logger
	 */

	'logging' => false,

	'logObject' => \Log::getMonolog(),

	'logPath' => storage_path('/logs/elasticsearch.log'),

	'logLevel' => Monolog\Logger::INFO,


	/**
	 * Retries
	 *
	 * By default, the client will retry n times, where n = number of nodes in
	 * your cluster. If you would like to disable retries, or change the number,
	 * you can do so here.
	 *
	 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#_set_retries
	 */

	'retries' => null,


	/**
	 * Index Environment Prefixing
	 *
	 * This is an addition to the Elasticsearch client.  Let's say your
	 * application has several different environments ("beta", "live", etc.).
	 * Normally, you could have different package configurations for each
	 * environment -- e.g., different "hosts".  But maybe you only have one ES
	 * instance available and you need to use it across all your environments.  You
	 * will start to run into difficulties, because the "beta" environment may
	 * index a document using the "my_index" index, and so will the "live"
	 * environment.  That is to say, both environments are using the same indices,
	 * so your data will get confused.
	 *
	 * By turning on environmentIndexPrefixing, we wrap the base Elasticsearch
	 * client object in our own object.  Then, we intercept all calls to the client
	 * and inspect the parameters being sent. If the "index" parameter exists, we
	 * prefix it with the current Laravel environment.  So "my_index" in the above
	 * scenario would become "beta_my_index" or "live_my_index".  You no longer
	 * will need to juggle different indices in your code -- we'll take care of it
	 * for you!
	 *
	 * Note: any data that's returned is not (currently) altered (e.g., to
	 * strip the environment from an index).
	 */

	'environmentIndexPrefixing' => false,


	/**
	 * The remainder of the configuration options can almost always be left
	 * as-is unless you have specific reasons to change them.  Refer to the
	 * appropriate sections in the Elasticsearch documentation for what each option
	 * does and what values it expects.
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
