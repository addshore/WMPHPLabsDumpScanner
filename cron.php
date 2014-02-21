<?php
if ( php_sapi_name() === 'cli' ) {
	define( 'DUMPSCAN_ENTRY', true );
}
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'init.php' );

$filesTodo = glob( DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR . '*.json');
$tasks = array();

echo "Got " . count( $filesTodo ) . " queries to do:\n";
echo "Moving queries to doing...\n";

foreach( $filesTodo as $file ) {
	echo " - {$file}\n";
	$fileDoing = substr_replace( $file, DIRECTORY_SEPARATOR .'doing' . DIRECTORY_SEPARATOR, strpos( $file, DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR ), strlen( '-todo-' ) );
	$deserialized = json_decode( file_get_contents( $file ) , true );
	$tasks[ $deserialized['dump'] ][ $fileDoing ] = \Mediawiki\Dump\DumpQuery::jsonDeserialize( $deserialized['query'] );
	rename ( $file, $fileDoing );
}
unset ( $filesTodo );

echo "\nGot " . count( $tasks ) . " tasks to do:\n";
foreach( $tasks as $dumpFile => $queries ) {
	$scanner = new \Mediawiki\Dump\DumpScanner( $dumpFile, $queries );
	$result = $scanner->scan();

	foreach( $queries as $queryKey => $query ) {
		$fileDone = substr_replace( $file, DIRECTORY_SEPARATOR . 'done' . DIRECTORY_SEPARATOR, strpos( $queryKey, DIRECTORY_SEPARATOR . 'doing' . DIRECTORY_SEPARATOR ), strlen( '-doing-' ) );
		$resFile = substr( $fileDone , 0, -3) . '.txt';
		file_put_contents( $resFile, implode( "\n", $result[$queryKey] ) );
		rename ( $queryKey, $fileDone );
	}
}