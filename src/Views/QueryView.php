<?php

namespace DumpScan\Views;

class QueryView {

	protected $hash;

	public function __construct( $queryHash ) {
		$this->hash = $queryHash;
	}

	public function getHtml() {
		foreach( array( 'todo', 'doing', 'done' ) as $state ) {
			$fileLocation = DUMPSCAN_STORE . DIRECTORY_SEPARATOR . $state . DIRECTORY_SEPARATOR . $this->hash . '.json';
			if( file_exists( $fileLocation ) ) {
				return file_get_contents( DUMPSCAN_STORE . '/todo/' . $this->hash . '.json' );
			}
		}
		return "Unknown query for hash {$this->hash}....";
	}
}