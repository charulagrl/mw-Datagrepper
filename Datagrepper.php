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
  return true;
}

function DatagrepperMessagesFunction( $parser, $username = '' ) {
  $parser->disableCache();

  $opts = array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Accept: text/html'
    )
  );

  $context  = stream_context_create( $opts );

  $result = file_get_contents(
    'https://apps.fedoraproject.org/datagrepper/raw?rows_per_page=5&order=desc&chrome=false&user=' .
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
