<?php

namespace DumpScan\Views;

use FilesystemIterator;
use HtmlObject\Element;

class IndexView {

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

	private function getBody() {
		$html = '';
		$html .= Element::create( 'h1', 'Dump Scan' );
		$html .= Element::create( 'p', 'Wikimedia Labs dump scanning tool' );
		$html .= Element::create( 'a', 'Queue a new dump scan', array( 'href' => 'index.php?action=new' ) );
		$html .= Element::create( 'h2', 'Stats' );
		$html .= Element::create( 'p', 'In Queue: ' . $this->getNumberInState( 'todo' ) );
		$html .= Element::create( 'p', 'In Doing: ' . $this->getNumberInState( 'doing' ) );
		$html .= Element::create( 'p', 'Done: ' . $this->getNumberInState( 'done' ) / 2 );
		return $html;
	}

	private function getNumberInState( $state ) {
		$fi = new FilesystemIterator( DUMPSCAN_STORE . DIRECTORY_SEPARATOR . $state );
		return iterator_count( $fi );
	}
}