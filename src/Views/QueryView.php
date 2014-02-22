<?php

namespace DumpScan\Views;

use DumpScan\DumpScan;
use HtmlObject\Element;

class QueryView {

	protected $hash;

	public function __construct( $queryHash ) {
		$this->hash = str_replace( '.json', '', $queryHash );
	}

	public function getHtml() {
		return Element::create( 'html',
			Element::create( 'head',
				Element::create( 'title', 'Dump Scanning Tool' )
			)
			.
			Element::create( 'body',
				$this->getBody()
			)
		);
	}

	public function getBody() {
		$queryLocation = $this->getQueryLocation();
		if( !$queryLocation ) {
			return "Unknown query for hash {$this->hash}....";
		}

		$dumpScan = DumpScan::jsonDeserialize( file_get_contents( $queryLocation['location'] ) );

		$state = $queryLocation['state'];

		$html = '';
		$html .= Element::create( 'h1', 'Query: ' . $dumpScan->getHash() );
		$html .= Element::create( 'h3', 'Current state: ' . $dumpScan->getQueryState() );

		$dumpScanView = new DumpScanView( $dumpScan ) ;
		$html .= $dumpScanView->getHtml();

		if( $state === 'done' ) {
			$html .= Element::create( 'hr' );
			$html .= Element::create( 'a', Element::create( 'h2', 'Result' ), array( 'href' => 'store/done/' . $dumpScan->getHash() . '.txt' ) );
			//TODO fix disgusting hack below to find query result file....
			$html .= Element::create( 'pre', file_get_contents( str_replace( '.json', '.txt', $queryLocation['location'] ) ) );
		}
		return $html;
	}

	/**
	 * @return bool|string
	 */
	private function getQueryLocation() {
		foreach( array( 'todo', 'doing', 'done' ) as $state ) {
			$fileLocation = DUMPSCAN_STORE . DIRECTORY_SEPARATOR . $state . DIRECTORY_SEPARATOR . $this->hash . '.json';
			if( file_exists( $fileLocation ) ) {
				return array( 'location' => $fileLocation, 'state' => $state );
			}
		}
		return false;
	}
}