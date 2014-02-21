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
echo "Doing Tasks:\n";
foreach( $tasks as $dumpFile => $queries ) {
	echo " - " . count( $queries ) . " in {$dumpFile}\n";

	if( substr_compare( $dumpFile, '.bz2', -strlen( '.bz2' ), strlen( '.bz2' ) ) === 0 ) {
		$dumpFile = "compress.bzip2://" . $dumpFile;
	}

	$scanner = new \Mediawiki\Dump\DumpScanner( $dumpFile, $queries );

	try{
		$result = $scanner->scan();
	} catch( RuntimeException $e ) {
		echo $e->getMessage() . "\n";
		//Move the queries back if we had an error
		foreach( $queries as $queryKey => $query ) {
			$fileNotDone = substr_replace( $file, DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR, strpos( $queryKey, DIRECTORY_SEPARATOR . 'doing' . DIRECTORY_SEPARATOR ), strlen( '-doing-' ) );
			rename ( $queryKey, $fileNotDone );
			echo "   - NotDone: {$fileNotDone}\n";
		}
		continue;
	}

	foreach( $queries as $queryKey => $query ) {
		$fileDone = substr_replace( $file, DIRECTORY_SEPARATOR . 'done' . DIRECTORY_SEPARATOR, strpos( $queryKey, DIRECTORY_SEPARATOR . 'doing' . DIRECTORY_SEPARATOR ), strlen( '-doing-' ) );
		$resFile = substr( $fileDone , 0, -4) . 'txt';
		file_put_contents( $resFile, implode( "\n", $result[$queryKey] ) );
		rename ( $queryKey, $fileDone );
		echo "   - Done: {$fileDone}\n";
	}

}

echo "Exiting!";