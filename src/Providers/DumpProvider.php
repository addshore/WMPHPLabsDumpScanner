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
		$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'LabsDumpScanner.DumpProvider.' . date("dmYH") . '.jssdsdfssson';
		if( file_exists( $tmp ) ) {
			return json_decode( file_get_contents( $tmp ), true );
		} else {
			$dumps = glob( DUMPSCAN_DUMPS );
			$dumps = $this->filterDumps( $dumps );
			file_put_contents( $tmp, json_encode( $dumps ) );
			return $dumps;
		}
	}

	/**
	 * @param array $dumps
	 * @returns array
	 */
	private function filterDumps( $dumps ) {
		$filteredDumps = array();
		sort( $dumps );
		foreach( array_reverse( $dumps ) as $dump ) {
			$key = substr( strrchr( $dump, "/" ) , 1 );
			if( strpos( $key, '-' ) !== false ) {
				list( $wiki ) = explode( '-', $key, 2 );
				if( !array_key_exists( $wiki, $filteredDumps ) ) {
					$filteredDumps[$wiki] = $dump;
				}
			} else {
				$filteredDumps[$key] = $dump;
			}

		}
		ksort( $filteredDumps );//restore alphabetical order...
		return $filteredDumps;
	}

} 