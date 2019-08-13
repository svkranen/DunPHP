<?php
	
namespace DunPHP;

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

require_once('autoload.php');

$Resource = new \DunPHP\Base\ResourceController();
$config = parse_ini_file($Resource->getConf());

### URL PARSER ###
$controller_path = '\\DunPHP\\Classes\\Controller\\';

# Set 'StandardController' with Action 'index' as default, without any parameters
$controller = 'Standard';
$action = 'index';
$params = [];

$ControllerClass = NULL;
$ControllerInstance = NULL;

if ($_SERVER["REQUEST_URI"] != $config["uri_path"]) {
	
	$searchStr = str_replace('/','\/',substr($config["uri_path"], 0, -1));
	$searchStr = str_replace('.','\.',$searchStr);
	
	$aRequest = explode('?',preg_replace('/^'.$searchStr.'/', '', $_SERVER["REQUEST_URI"]));
	$aRoute = explode('/',substr($aRequest[0],1));
	
	if (isset($aRoute[0])) {
		if (class_exists($controller_path.$aRoute[0].'Controller')) {
			$controller = $aRoute[0];
			$ControllerClass = new \ReflectionClass($controller_path.$controller.'Controller');
			$ControllerInstance = $ControllerClass->newInstance();
			if (method_exists($ControllerInstance,$aRoute[1].'Action')) { 
				$action = $aRoute[1];
			}
		
		} else {
			$ControllerClass = new \ReflectionClass($controller_path.$controller.'Controller');
			$ControllerInstance = $ControllerClass->newInstance();
			if (method_exists($ControllerInstance,$aRoute[0].'Action')) { 
				$action = $aRoute[0];
			}
		
		}
	}
	
	if (isset($aRequest[1])) {
		foreach (explode('&',$aRequest[1]) as $param_line) {
			list ($pKey, $pValue) = explode('=',$param_line,2);
			$params[$pKey] = $pValue;
		}
	}
} else {
	$ControllerClass = new \ReflectionClass($controller_path.$controller.'Controller');
	$ControllerInstance = $ControllerClass->newInstance();
}

if ($_POST) {
	foreach ($_POST as $key => $value) {
		$params[$key] = $value;
	}
}

# ENABLE CACHING
if ($config["caching"] == true) {
	$controllerAction = $controller.$action;
	$cachefile = 'Cache/cached-'.$controllerAction.'.html';
	$cachetime = 18000;
 
	// Serve from the cache if it is younger than $cachetime
	if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
		echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
		include($cachefile);
		exit;
	}
	ob_start(); // Start the output buffer
}

try {
	call_user_func(array($ControllerInstance,$action.'Action'),$params);
} catch (Exception $e) {
	echo 'Fehler beim Ausführen der Funktion '.$action.'Action in der Klasse '.get_class($ControllerInstance).' : ', $e->getMessage(), '\n';
}

if ($config["caching"] == true) {
	// Cache the contents to a file
	$cached = fopen($cachefile, 'w');
	fwrite($cached, ob_get_contents());
	fclose($cached);
	ob_end_flush(); // Send the output to the browser
}

?>