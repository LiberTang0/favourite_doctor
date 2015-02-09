<?php
ini_set('display_errors', 1); 
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
	
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

 
    defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/library'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
//echo "<pre>";print_r($_SERVER);exit;
if(isset($_SERVER['HTTP_HOST'])){
    $HTTP_HOST = $_SERVER['HTTP_HOST'];
}else{
    $HTTP_HOST = '';
}

        define('APPLICATION_INI', "application.ini");
     
//define('APPLICATION_INI', "application_domain.ini");
//die(APPLICATION_INI);
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/'.APPLICATION_INI
);



 	
/*$application->bootstrap()
            ->run();
*/



$arrR=explode("?", $_SERVER['REQUEST_URI']);
//var_dump($arrR);;
if($arrR[0]=="/search/"){
	/*---Cache Start---*/
	$time=60*60*24*1;
	$uri =$HTTP_HOST.$_SERVER['REQUEST_URI'];
	$key=md5($uri);
	//$key=md5($_SERVER['REQUEST_URI']);
	$frontendOptions = array('lifeTime' =>$time,'automatic_serialization' => true); // 7200 seconds
	$backendOptions = array('cache_dir' => PUBLIC_PATH.'/cache/');
	$cache = Zend_Cache::factory('Output', 'File', $frontendOptions, $backendOptions);
	//$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	
	if (!$cache->start($key) ){
		
		$application->bootstrap()
				->run();	
		$cache->end();
		
	} 
	/*---Cache End---*/
}else{
    
    $application->bootstrap()
            ->run();	
}




function prexit($array){
    if($_SERVER['HTTP_HOST']=='localhost.dih.com'){
        echo "<pre>";print_r($array);exit;
    }
}
function pre($array){
    if($_SERVER['HTTP_HOST']=='localhost.dih.com'){
        echo "<pre>";print_r($array);
    }
}

/** Zend_Application */
require_once APPLICATION_PATH.'/configs/lang/config.php';