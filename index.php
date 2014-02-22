<?php
if ( php_sapi_name() !== 'cli' ) {
	define( 'DUMPSCAN_ENTRY', true );
}
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'init.php' );

if ( !empty( $_POST ) ) {

	$query = getQueryFromPostData( $_POST );
	if( $query->getConditionCount() === 0 ) {
		throw new Exception( 'No query conditions provided' );
	} else {
		$dumpScan = new DumpScan\DumpScan( $_POST['dump'], $query );
		$dumpScan->create( $query );
		header( 'Location: index.php?action=query&id=' . $dumpScan->getHash() );
		return;
	}

}

if ( !empty( $_GET ) && array_key_exists( 'action', $_GET ) ) {
	switch ( strtolower( $_GET['action'] ) ) {
		case 'new':
			$form = new DumpScan\Views\NewQueryView();
			echo $form->getHtml();
			return;
		case 'log':
			if( !array_key_exists( 'imascript', $_GET ) ) {
				$form = new DumpScan\Views\LogView();
				echo $form->getHtml();
				return;
			} else {
				$filename = __DIR__ . DIRECTORY_SEPARATOR . 'cron.log';
				echo file_get_contents( $filename );
				return;
			}
		case 'query':
			if( !array_key_exists( 'id', $_GET ) ) {
				throw new Exception( 'When looking for a query you must specify the ID' );
			}
			$view = new DumpScan\Views\QueryView( $_GET['id'] );
			echo $view->getHtml();
			return;
	}

}

$view = new DumpScan\Views\IndexView();
echo $view->getHtml();
return;

/**
 * @param $postData
 *
 * @throws Exception
 * @returns \Mediawiki\Dump\DumpQuery
 */
function getQueryFromPostData( $postData ) {
	if( !array_key_exists( 'dump', $postData ) || empty( $postData['dump'] ) ) {
		throw new Exception( 'No dump provided' );
	}

	$query = new \Mediawiki\Dump\DumpQuery();

	if( array_key_exists( 'nsinclude', $postData ) ) {
		foreach( $postData['nsinclude'] as $ns ) {
			$query->addNamespaceFilter( intval( $ns ) );
		}
	}

	if( array_key_exists( 'titlecontains', $postData ) && !empty( $postData['titlecontains'] ) ) {
		$query->addTitleFilter( $postData['titlecontains'], \Mediawiki\Dump\DumpQuery::TYPE_CONTAINS );
	}
	if( array_key_exists( 'titlemissing', $postData ) && !empty( $postData['titlemissing'] ) ) {
		$query->addTitleFilter( $postData['titlemissing'], \Mediawiki\Dump\DumpQuery::TYPE_MISSING );
	}
	if( array_key_exists( 'textcontains', $postData ) && !empty( $postData['textcontains'] ) ) {
		$query->addTextFilter( $postData['textcontains'], \Mediawiki\Dump\DumpQuery::TYPE_CONTAINS );
	}
	if( array_key_exists( 'textmissing', $postData ) && !empty( $postData['textmissing'] ) ) {
		$query->addTextFilter( $postData['textmissing'], \Mediawiki\Dump\DumpQuery::TYPE_MISSING );
	}
	return $query;
}