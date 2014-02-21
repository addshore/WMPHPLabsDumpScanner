<?php

namespace DumpScan\Views;

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
		return "Index Not Yet Implemented...  try ?action=new";
	}
}