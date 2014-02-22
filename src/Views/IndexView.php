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
		$html .= Element::create( 'small', ' (this page can sometimes be slow to load)' );
		$html .= Element::create( 'h2', 'Status' );
		$html .= Element::create( 'a', 'View my log', array( 'href' => 'index.php?action=log' ) );

		$todo = new FilesystemIterator( DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'todo' );
		$html .= Element::create( 'p', 'In Queue: ' . ( iterator_count( $todo ) -1 ) );
		$list = '';
		//TODO rather than outputing in this random order perhaps order by date tocuhed or similar?
		foreach( $todo as $fileInfo ) {
			$name = $fileInfo->getFilename();
			if( strstr( $name , '.json' ) && $name != '.gitignore' ) {
				$list .= Element::create( 'li', Element::create( 'a', $fileInfo->getFilename(), array( 'href' => 'index.php?action=query&id='.$fileInfo->getFilename() ) ) );
			}
		}
		$html .= Element::create( 'ul', $list );

		$doing = new FilesystemIterator( DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'doing' );
		$html .= Element::create( 'p', 'In Doing: ' . ( iterator_count( $doing ) -1 ) );
		$list = '';
		//TODO rather than outputing in this random order perhaps order by date tocuhed or similar?
		foreach( $doing as $fileInfo ) {
			$name = $fileInfo->getFilename();
			if( strstr( $name , '.json' ) && $name != '.gitignore' ) {
				$list .= Element::create( 'li', Element::create( 'a', $fileInfo->getFilename(), array( 'href' => 'index.php?action=query&id='.$fileInfo->getFilename() ) ) );
			}
		}
		$html .= Element::create( 'ul', $list );

		$done = new FilesystemIterator( DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'done' );
		$html .= Element::create( 'p', 'Done: ' .  ( ( iterator_count( $done ) - 1 ) / 2 ) );
		$list = '';
		//TODO rather than outputing in this random order perhaps order by date tocuhed or similar?
		foreach( $done as $fileInfo ) {
			$name = $fileInfo->getFilename();
			if( strstr( $name , '.json' ) && $name != '.gitignore' ) {
				$list .= Element::create( 'li', Element::create( 'a', $fileInfo->getFilename(), array( 'href' => 'index.php?action=query&id='.$fileInfo->getFilename() ) ) );
			}
		}
		$html .= Element::create( 'ul', $list );

		return $html;
	}

}