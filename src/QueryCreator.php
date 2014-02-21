<?php

namespace DumpScan;

use Mediawiki\Dump\DumpQuery;

class QueryCreator {

	/**
	 * @var DumpQuery
	 */
	protected $query;

	/**
	 * @var string
	 */
	protected $dumpFile;

	/**
	 * @param string $dumpFile
	 * @param DumpQuery $query
	 */
	public function __construct( $dumpFile, DumpQuery $query ) {
		$this->dumpFile = $dumpFile;
		$this->query = $query;
	}

	/**
	 * @return string query hash
	 */
	public function create() {
		$this->basicSetup();
		file_put_contents(
			DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR . $this->query->getHash() . '.json',
			json_encode( array( 'dump' => $this->dumpFile, 'query' => $this->query->jsonSerialize() ) )
		);
		return $this->query->getHash();
	}

	/**
	 * Ensures our environment is correctish
	 */
	private function basicSetup() {
		$folders = array(
			DUMPSCAN_STORE,
			DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'todo',
			DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'doing',
			DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'done',
		);

		foreach( $folders as $folder ) {
			if( !is_dir( $folder ) ) {
				mkdir( $folder );
			}
		}
	}

} 