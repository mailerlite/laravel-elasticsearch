<?php

/**
 * There are several options that you can use to customize the
 * Elasticsearch client, but these are main ones.
 *
 * If you want details on the other configuration options, they
 * are documented on the main ES website:
 *
 * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_configuration.html
 */


return [

	/**
	 * Array of hosts that Elasticsearch will connect to.
	 */

	'hosts' => [
		'localhost:9200'
	],

	/**
	 * If you want to enable and/or customize the logging, update the following
	 * settings.
	 */

	'logging' => false,

	'logObject' => null,

	'logPath' => storage_path('/logs/elasticsearch.log'),

	'logLevel' => Psr\Log\LogLevel::WARNING,

];
