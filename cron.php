<?php
if ( php_sapi_name() === 'cli' ) {
	define( 'DUMPSCAN_ENTRY', true );
}
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'init.php' );

$cronpid = getmypid();

function cronecho( $message ) {
	global $cronpid;
	echo $message . "\n";
	$toLog = "[" . date("Y/m/d h:i:s", time()) . "] - $cronpid - " . $message . "\n";
	file_put_contents( __DIR__ . DIRECTORY_SEPARATOR . 'cron.log', $toLog, FILE_APPEND );
}

cronecho( 'Cron Started on ' . gethostname() . ' with pid ' . $cronpid );

$filesTodo = glob( DUMPSCAN_STORE . DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR . '*.json');
$tasks = array();

cronecho( "Got " . count( $filesTodo ) . " queries" );
if( count( $filesTodo ) !== 0 ) {
	foreach( $filesTodo as $file ) {
		cronecho( " - {$file}" );
		$fileDoing = substr_replace( $file, DIRECTORY_SEPARATOR .'doing' . DIRECTORY_SEPARATOR, strpos( $file, DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR ), strlen( '-todo-' ) );
		$deserialized = json_decode( file_get_contents( $file ) , true );
		$tasks[ $deserialized['dump'] ][ $fileDoing ] = \Mediawiki\Dump\DumpQuery::jsonDeserialize( $deserialized['query'] );
		rename ( $file, $fileDoing );
	}
	unset ( $filesTodo, $fileDoing, $file );

	cronecho( "Got " . count( $tasks ) . " tasks to run" );
	foreach( $tasks as $dumpFile => $queries ) {
		//If we are using a bzip2 add the compress to the URI
		if( substr_compare( $dumpFile, '.bz2', - 4, 4 ) === 0 ) {
			$dumpFile = "compress.bzip2://" . $dumpFile;
		}

		cronecho( " - " . count( $queries ) . " in {$dumpFile}" );
		$scanner = new \Mediawiki\Dump\DumpScanner( $dumpFile, $queries );

		try{
			$result = $scanner->scan();
		} catch( RuntimeException $e ) {
			cronecho( $e->getMessage() );
			//Move the queries back if we had an error
			foreach( $queries as $queryKey => $query ) {
				$fileNotDone = substr_replace( $queryKey, DIRECTORY_SEPARATOR . 'todo' . DIRECTORY_SEPARATOR, strpos( $queryKey, DIRECTORY_SEPARATOR . 'doing' . DIRECTORY_SEPARATOR ), strlen( '-doing-' ) );
				rename ( $queryKey, $fileNotDone );
				cronecho( "   - NotDone: {$fileNotDone}" );
			}
			continue;
		}

		foreach( $queries as $queryKey => $query ) {
			$fileDone = substr_replace( $queryKey, DIRECTORY_SEPARATOR . 'done' . DIRECTORY_SEPARATOR, strpos( $queryKey, DIRECTORY_SEPARATOR . 'doing' . DIRECTORY_SEPARATOR ), strlen( '-doing-' ) );
			$resFile = substr( $fileDone , 0, -5 /* 5 chars of .json */ ) . '.txt';
			file_put_contents( $resFile, implode( "\n", $result[$queryKey] ) );
			rename ( $queryKey, $fileDone );
			cronecho( "   - Done: {$resFile}" );
		}

	}
}

cronecho( "Exiting" );
