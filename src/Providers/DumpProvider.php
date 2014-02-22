<?php

namespace DumpScan\Providers;

class DumpProvider {

	/**
	 * Get list of dumps with REALLY basic caching
	 * @return array
	 */
	public function get() {
		$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'LabsDumpScanner.DumpProvider.' . date("dmYH");
		if( file_exists( $tmp ) ) {
			return explode( "\n", file_get_contents( $tmp ) );
		} else {
			$dumps = glob( DUMPSCAN_DUMPS );
			file_put_contents( $tmp, implode( "\n", $dumps ) );
			return $dumps;
		}
	}

} 