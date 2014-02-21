<?php

namespace DumpScan;

/**
 * @todo allow this to get namespaces for a specific wiki?
 */
class NamespaceProvider {

	public function get() {
		return array(
			0 => 'Main',
			1 => 'Talk',
			2 => 'User',
			3 => 'User talk',
			4 => 'Project',
			5 => 'Project talk',
			6 => 'File',
			7 => 'File Talk',
			8 => 'MediaWiki',
			9 => 'MediaWiki Talk',
			10 => 'Template',
			11 => 'Template Talk',
			12 => 'Help',
			13 => 'Help Talk',
			14 => 'Category',
			15 => 'Category Talk',
		);
	}

} 