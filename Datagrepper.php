<?php
$wgExtensionCredits['parserhook'][] = array(
    'path' => __FILE__,
    'name' => 'Datagrepper',
    'author' => 'Charul',
    'url' => 'https://github.com/charulagrl/mw-Datagrepper',
    'description' => "This extension provides a syntax for displaying one's messages",
    'version'  => 1.1,
);

$wgHooks['ParserFirstCallInit'][] = 'DatagrepperSetupParserFunction';
$wgExtensionMessagesFiles['Datagrepper'] = dirname( __FILE__ ) . '/Datagrepper.i18n.php';

function DatagrepperSetupParserFunction( &$parser ) {
  $parser->setFunctionHook( 'datagreppermessages', 'DatagrepperMessagesFunction' );
  $parser->setFunctionHook( 'datagreppertable', 'DatagrepperTableFunction' );
  return true;
}

function DatagrepperMessagesFunction( $parser, $username = '', $msg_count = 5) {
  $parser->disableCache();

  $opts = array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Accept: text/html'
    )
  );

  $context  = stream_context_create( $opts );

  $result = @file_get_contents(
    'https://apps.fedoraproject.org/datagrepper/raw?rows_per_page='.urlencode($msg_count).'&order=desc&chrome=false&user=' .
    urlencode( $username ),
    false,
    $context);

  $stripped = preg_replace( '/^\s*/m', '', $result );

  return array(
    $stripped,
    'isHTML' => true,
    'nowiki' => true
  );
}

/**
 * Functions much like DatagrepperMessagesFunction but displays a pretty table
 * instead of raw HTML that contains who-knows-what.
 */
function DatagrepperTableFunction( $parser, $username, $msg_count = 5 ) {
  $parser->disableCache();

  $opts = array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Accept: application/json'
    )
  );

  $context  = stream_context_create( $opts );

  $json= @file_get_contents(
    'https://apps.fedoraproject.org/datagrepper/raw?rows_per_page='.urlencode($msg_count).'&order=desc&user=' .
    urlencode( $username ) . '&meta=date&meta=subtitle&meta=title',
    false,
    $context);

  $json_decoded = json_decode( $json, true );
  if ( $json_decoded === NULL || !array_key_exists( 'raw_messages', $json_decoded) ) {
    return array ( 'Failed to decode JSON.', 'isHTML' => true );
  }

  $table = array();
  $table[] = '{| class="wikitable"';
  $table[] = '!colspan="6"|Recent Actions';
  $table[] = '|-';
  $table[] = '!Topic';
  $table[] = '!Summary';
  $table[] = '!Time';

  foreach ( $json_decoded['raw_messages'] as $message ) {
    $table[] = '|-';
    $table[] = '|' . $message['meta']['title'];
    $table[] = '|' . $message['meta']['subtitle'];
    $table[] = '|' . $message['meta']['date'];
  }

  $table[] = '|}';

  $src = implode( "\n", $table );

  return array( $src, 'nowiki' => false, 'noparse' => false );
}
