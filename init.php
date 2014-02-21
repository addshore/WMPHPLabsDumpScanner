<?php
if( !is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	die( 'Can not read autoload.php' );
}

if( !defined( 'DUMPSCAN_ENTRY' ) ) {
	die( 'Not a valid entry point' );
}

require_once( __DIR__ . '/vendor/autoload.php' );

define( 'DUMPSCAN_STORE', __DIR__ . '/store/' );

if( is_readable( '/public/datasets/public/' ) ) {
	define( 'DUMPSCAN_DUMPS', '/public/datasets/public/*/*/*-pages-meta-current.xml.bz2' );
} else {
	define( 'DUMPSCAN_DUMPS', __DIR__ . '/vendor/addwiki/mediawiki-dump/tests/*.xml' );
}
