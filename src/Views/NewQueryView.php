<?php

namespace DumpScan\Views;

use DumpScan\Providers\DumpProvider;
use DumpScan\Providers\NamespaceProvider;
use HtmlObject\Element;

class NewQueryView {

	/**
	 * @var \DumpScan\Providers\DumpProvider
	 */
	protected $dumpProvider;
	/**
	 * @var \DumpScan\Providers\NamespaceProvider
	 */
	protected $namespaceProvider;

	public function __construct() {
		$this->namespaceProvider = new NamespaceProvider();
		$this->dumpProvider = new DumpProvider();
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

	private function getBody() {
		return Element::create( 'form', $this->getFormContent(), array( 'action' => 'index.php', 'method' => 'POST' ) );
	}

	private function getFormContent() {
		$html = '';
		$html .= Element::create( 'h1', 'Create a new Dump Scan' );
		$html .= Element::create( 'h3', 'Select a Dump' );
		$html .= Element::create( 'select', $this->getDumpOptions(), array( 'name' => 'dump' ) );
		$html .= Element::create( 'h3', 'Include Namespaces' );
		$html .= Element::create( 'ol', $this->getNamespacesItems());
		$html .= Element::create( 'h3', 'Title' );
		$html .= 'Contains: ' . $this->getRegexInputBox( 'title', 'contains' );
		$html .= Element::create( 'br' );
		$html .= 'Missing: ' . $this->getRegexInputBox( 'title', 'missing' );
		$html .= Element::create( 'h3', 'Text' );
		$html .= 'Contains: ' . $this->getRegexInputBox( 'text', 'contains' );
		$html .= Element::create( 'br' );
		$html .= 'Missing: ' . $this->getRegexInputBox( 'text', 'missing' );
		$html .= Element::create( 'br' );
		$html .= Element::create( 'input', '', array( 'type' => 'submit' ) );
		return $html;
	}

	private function getDumpOptions() {
		$html = '';
		foreach( $this->dumpProvider->get() as $dump ) {
			$html .= Element::create( 'option', substr( strrchr( $dump, "/" ) , 1 ) , array( 'value' => $dump ) );
		}
		return $html;
	}

	private function getNamespacesItems() {
		$html = '';
		foreach( $this->namespaceProvider->get() as $key => $ns ) {
			$html .= Element::create( 'li',
				Element::create( 'label',
					Element::create( 'input', $ns, array(
						'type' => 'checkbox',
						'name' => 'nsinclude[]',
						'value' => $key,
					) )
				)
			);
		}
		return $html;
	}

	private function getRegexInputBox( $what, $searchType ) {
		return Element::create( 'input', '', array( 'type' => 'textbox', 'name' => $what . $searchType ) );
	}

}