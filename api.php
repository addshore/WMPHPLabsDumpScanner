<?php
if ( php_sapi_name() !== 'cli' ) {
	define( 'DUMPSCAN_ENTRY', true );
}
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'init.php' );

//TODO implement a nice form of api to do nice stuff!