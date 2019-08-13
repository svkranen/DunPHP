<?php

namespace DunPHP\Classes\Controller;

require_once(__DIR__.'/../../autoload.php');

class StandardController implements IActionController
{
	
	private $View;
	
	# 
	private $URL_CONTROLLER = '';
	private $URL_ACTION = '';
	private $placeholder_URL_Controller = "#_URLCONTROLLER_#";
	private $placeholder_URL_Action = "#_URLACTION_#";
	
	public function __construct() {
		$this->View = new \DunPHP\Base\ViewController(static::class);
		return $this;
	}
	
	/***
	 * Helper Function to extract $URL_CONTROLLER and $URL_Action from URI!
	 * 
	 * @var String $function
	 ***/
	public function getControllerAction($function) {
		$Resource = new \DunPHP\Base\ResourceController();
		
		$aControllerAction = explode("/",str_replace($Resource->getURI_Path(),'',$_SERVER["REDIRECT_URL"]));
		$strController = substr(strrchr(str_replace('Controller','',get_class($this)),'\\'),1);
	
		if ($aControllerAction[0] == $strController) {
			$this->URL_CONTROLLER = $aControllerAction[0];
		
			if(isset($aControllerAction[1])) {
				if ($aControllerAction[1] == str_replace('Action','',$function)) {
				$this->URL_ACTION = $aControllerAction[1];
				}
			}			
		} elseif ($aControllerAction[0] == str_replace('Action','',$function)) {
			$this->URL_ACTION = $aControllerAction[0];
		}
	}
	
	public function indexAction($params = '') {
		$content = '';
		
		if ($this->View) {
			try {
				$content = $this->View->getTemplate(__FUNCTION__);
			} catch (Exception $e) {
				echo 'Fehler beim Laden des Templates: '.$e->getMessage().'\n';
			}
		} else {
			echo 'Kann das Template '.print_r($this->View).' nicht laden für '.__FUNCTION__.'!';
		}
		
		echo $content;
	}
}