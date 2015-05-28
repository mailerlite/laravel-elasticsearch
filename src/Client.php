<?php namespace Cviebrock\LaravelElasticsearch;

use Elasticsearch\Client as BaseClient;


/**
 * Class Client decorates the base Elasticsearch client to allow environment
 * index prefixing.
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class Client {

	/**
	 * @var BaseClient
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $prefix = null;

	/**
	 * @param BaseClient $client
	 * @param string $prefix
	 */
	public function __construct(BaseClient $client, $prefix) {

		$this->setClient($client);
		$this->setPrefix($prefix);
	}

	/**
	 * @param BaseClient $client
	 */
	public function setClient(BaseClient $client) {
		$this->client = $client;
	}

	/**
	 * Set the string to prefix to all indices.
	 *
	 * @param string $prefix
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * Magic method to pass calls to the underlying ES client,
	 * prepending the index/indices if required.
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {

		if ($this->prefix !== null
			&& (isset($arguments[0]) === true)
			&& (isset($arguments[0]['index']) === true)
		) {
			$arguments[0]['index'] = $this->prefixIndices($arguments[0]['index']);
		}

		return call_user_func_array([$this->client, $name], $arguments);
	}

	/**
	 * Prepend the prefix to all the given indices.
	 *
	 * @param array|string $indices
	 * @return array|string
	 */
	protected function prefixIndices($indices) {

		if (is_string($indices)) {
			$indices = $this->prefixIndex($indices);
		} else if (is_array($indices)) {
			foreach ($indices as $key => $index) {
				$indices[$key] = $this->prefixIndex($index);
			}
		}

		return $indices;
	}

	/**
	 * Prepend the prefix to a string index.
	 *
	 * @param string $index
	 * @return string
	 */
	protected function prefixIndex($index) {
		$first = substr($index, 0, 1);
		if ($first === '+' || $first === '-') {
			$index = $first . $this->prefix . substr($index, 1);
		} else {
			$index = $this->prefix . $index;
		}

		return $index;
	}
}
