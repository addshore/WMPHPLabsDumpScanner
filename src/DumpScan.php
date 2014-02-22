<?php

namespace DumpScan;

use InvalidArgumentException;
use Mediawiki\Dump\DumpQuery;
use RuntimeException;

class DumpScan {

	protected $query;
	protected $target;

	/**
	 * @param DumpQuery $query
	 * @param string $dumpTarget
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $dumpTarget, DumpQuery $query ) {
		if( !is_string( $dumpTarget ) ) {
			throw new InvalidArgumentException( '$dumpTarget must be a string' );
		}

		$this->query = $query;
		$this->target = $dumpTarget;
	}

	public function getQuery() {
		return $this->query;
	}

	public function getTarget() {
		return $this->target;
	}

	/**
	 * @param string $state
	 *
	 * @return string
	 */
	public function getStoreLocation( $state ) {
		$this->assertCorrectState( $state );
		return DUMPSCAN_STORE . DIRECTORY_SEPARATOR . $state . DIRECTORY_SEPARATOR . $this->getHash() . '.json';
	}

	/**
	 * @return string
	 */
	public function getResultLocation() {
		return DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'done' . DIRECTORY_SEPARATOR . $this->getHash() . '.txt';
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return sha1( json_encode( $this->jsonSerialize() ) );
	}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @todo when we move to 5.4 add the interface...
	 */
	public function jsonSerialize() {
		return array(
			'query' => $this->query->jsonSerialize(),
			'dump' => $this->target,
		);
	}

	/**
	 * @param string|array $json
	 *
	 * @throws InvalidArgumentException
	 * @return DumpScan
	 */
	public static function jsonDeserialize( $json ) {
		if( !is_array( $json ) && !is_string( $json ) ) {
			throw new InvalidArgumentException( 'jsonDeserialize needs an array or string' );
		}

		if( is_string( $json ) ) {
			$array = json_decode( $json, true );
		} else {
			$array = $json;
		}

		return new self( $array['dump'], DumpQuery::jsonDeserialize( $array['query'] ) );
	}

	private function assertCorrectState( $state ) {
		if( !in_array( $state, array( 'todo', 'doing', 'done' ) ) ) {
			throw new InvalidArgumentException( '$state must be one of todo|doing|done' );
		}
	}

	/**
	 * @throws RuntimeException
	 * @return string query hash
	 */
	public function create() {
		if( $this->getQueryState() !== 'unknown' ) {
			throw new RuntimeException( 'DumpScan already exists' );
		}
		return file_put_contents( $this->getStoreLocation( 'todo' ), json_encode( $this->jsonSerialize() ) );
	}

	public function getQueryState() {
		foreach( array( 'todo', 'doing', 'done' ) as $state ) {
			$fileLocation = $this->getStoreLocation( $state );
			if( file_exists( $fileLocation ) ) {
				return $state;
			}
		}
		return 'unknown';
	}

} 