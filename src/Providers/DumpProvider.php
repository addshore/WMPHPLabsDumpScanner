<?php

namespace DumpScan\Providers;

/**
 * @author Adam Shorland
 */
class DumpProvider {

	/**
	 * Get list of dumps with REALLY basic caching
	 * @return array
	 * @todo generation of this should perhaps be on a cron to avoid a slow page load every hour
	 */
	public function get() {
		$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'LabsDumpScanner.DumpProvider.' . date("dmYH");
		if( file_exists( $tmp ) ) {
			return explode( "\n", file_get_contents( $tmp ) );
		} else {
			$dumps = glob( DUMPSCAN_DUMPS );
			file_put_contents( $tmp, implode( "\n", $dumps ) );
			//@todo rather than returning multiple dumps for single wikis perhaps we should filter this a bit?
			return $dumps;
		}
	}

} 