<?php

namespace DumpScan\Providers;

class DumpProvider {

	/**
	 * @return array
	 */
	public function get() {
		return glob( DUMPSCAN_DUMPS );
	}

} 