<?php

namespace DumpScan\Views;

use HtmlObject\Element;

class QueryView {

	protected $hash;

	public function __construct( $queryHash ) {
		$this->hash = $queryHash;
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

		$json = file_get_contents( $queryLocation['location'] );
		$state = $queryLocation['state'];

		$html = '';
		$html .= Element::create( 'h1', $this->hash );
		$html .= Element::create( 'h3', 'Current state: ' . $state );
		$html .= Element::create( 'p', $json );

		if( $state === 'done' ) {
			$html .= Element::create( 'hr' );
			$html .= Element::create( 'h2', 'Result' );
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