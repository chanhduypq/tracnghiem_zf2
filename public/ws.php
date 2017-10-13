<?php
//ini_set( 'magic_quotes_gpc', 0 );
iconv_set_encoding("internal_encoding", "UTF-8");
require_once 'define.php';
// Typically, you will also want to add your library/ directory
// to the include_path, particularly if it contains your ZF installed
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(dirname(__FILE__)) . '/library',
    get_include_path(),
))); 

require_once 'Zend/Application.php';
require_once "Zend/Loader.php";

Zend_Loader::loadClass('WsClass_General');

            

//Zend_Loader::registerAutoload();



require_once 'Core.php';
$environment = APPLICATION_ENV;
$options = APPLICATION_PATH . '/configs/application.ini';
$application = new Zend_Application($environment, $options);

if (isset($_GET['wsdl'])) {
    $wsdl  = new Zend_Soap_AutoDiscover();
    $wsdl->setClass('WsClass_General');             
    $wsdl->handle();
} else {                          
    $uri =  'http://tracnghiem.local/ws.php?wsdl';
    try {        
      $soap_server = new Zend_Soap_Server($uri, array('soap_version' => SOAP_1_2));
      $soap_server->setClass('WsClass_General');
      $soap_server->handle();                
        
    } catch(Exception $e){
        
       die('ERROR :'.$e->getMessage());        
       
    }            
}






