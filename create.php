<?php
if ( php_sapi_name() !== 'cli' ) {
	define( 'DUMPSCAN_ENTRY', true );
}
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'init.php' );

//init stuff
$lastError = null;

//Was it posted?
if ( !empty( $_POST ) ) {

	if( !array_key_exists( 'dump', $_POST ) || empty( $_POST['dump'] ) ) {
		$lastError = 'No dump provided';
	} else {
		$query = new \Mediawiki\Dump\DumpQuery();

		if( array_key_exists( 'nsinclude', $_POST ) ) {
			foreach( $_POST['nsinclude'] as $ns ) {
				$query->addNamespaceFilter( intval( $ns ) );
			}
		}

		if( array_key_exists( 'titlecontains', $_POST ) && !empty( $_POST['titlecontains'] ) ) {
			$query->addTitleFilter( $_POST['titlecontains'], \Mediawiki\Dump\DumpQuery::TYPE_CONTAINS );
		}
		if( array_key_exists( 'titlemissing', $_POST ) && !empty( $_POST['titlemissing'] ) ) {
			$query->addTitleFilter( $_POST['titlemissing'], \Mediawiki\Dump\DumpQuery::TYPE_MISSING );
		}
		if( array_key_exists( 'textcontains', $_POST ) && !empty( $_POST['textcontains'] ) ) {
			$query->addTextFilter( $_POST['textcontains'], \Mediawiki\Dump\DumpQuery::TYPE_CONTAINS );
		}
		if( array_key_exists( 'textmissing', $_POST ) && !empty( $_POST['textmissing'] ) ) {
			$query->addTextFilter( $_POST['textmissing'], \Mediawiki\Dump\DumpQuery::TYPE_MISSING );
		}

		if( $query->getConditionCount() === 0 ) {
			$lastError = 'No query conditions provided';
		} else {
			$queryRegister = new DumpScan\QueryCreator( $_POST['dump'], $query );
			$hash = $queryRegister->create( $query );
			echo "New query is {$hash}";
			return;
		}

	}
}

//default
$form = new DumpScan\NewDumpScanForm( $lastError );
echo $form->getHtml();