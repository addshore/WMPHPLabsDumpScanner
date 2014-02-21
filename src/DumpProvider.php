<?php

namespace DumpScan;

class DumpProvider {

	/**
	 * @return array
	 */
	public function get() {
		return $this->rglob( DUMPSCAN_DUMPS . DIRECTORY_SEPARATOR . '*.xml');
	}

	private function rglob( $pattern, $flags = 0 )
	{
		$files = glob( $pattern, $flags );
		foreach ( glob( dirname( $pattern ) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT ) as $dir )
		{
			$files = array_merge( $files, $this->rglob( $dir . DIRECTORY_SEPARATOR . basename( $pattern ), $flags ) );
		}
		return $files;
	}

} 