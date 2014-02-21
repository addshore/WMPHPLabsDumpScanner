<?php

namespace DumpScan;

class DumpProvider {

	/**
	 * @return array
	 */
	public function get() {
		return glob( DUMPSCAN_DUMPS );
	}

} 