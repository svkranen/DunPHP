<?php

namespace DunPHP\Base;

require_once(__DIR__.'/../autoload.php');

class ResourceController
{
	private $URI_Path;
	private $PrivatePath;
	private $PublicPath;
	private $Configuration;
	private $BaseURI;
	
	public function __construct()
	{
		$config = parse_ini_file(__DIR__.'/../Configuration/config.ini');
		$this->URI_Path = $config["uri_path"];
		$this->Configuration = $_SERVER["DOCUMENT_ROOT"].$config["uri_path"].'Configuration/config.ini';
		$this->PublicPath = $_SERVER["SERVER_NAME"].$config["uri_path"].'Resources/Public/';
		$this->PrivatePath = $_SERVER["DOCUMENT_ROOT"].$config["uri_path"].'Resources/Private/';
		$this->BaseURI = $_SERVER["SERVER_NAME"].$config["uri_path"];
	}
	
	public function getPublicFile($relativePath) {
		$fullPath = $this->PublicPath.$relativePath;
		$fullPath = str_replace($_SERVER["SERVER_NAME"],'',$fullPath);
		// if (file_exists($fullPath)) {
			return $fullPath;
		// } else return false;
	}
	
	public function getPrivateFile($relativePath) {
		$fullPath = $this->PrivatePath.$relativePath;
		if (file_exists($fullPath)) {
			return $fullPath;
		} else return false;
	}
	
	// get a fully qualified link within this microsite
	public function getILink($iLink) {
		$fullLink = ($_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://").$this->BaseURI.preg_replace('/^\/+/', '', $iLink);
		return $fullLink;
	}
	
	public function getConf() {
		return $this->Configuration;
	}
	
	public function getURI_Path() {
		return $this->URI_Path;
	}
	
	public function getBaseURI() {
		return $this->BaseURI;
	}
	
	public function getControllerAction() {
		return $this->getILink(str_replace($this->URI_Path,'',$_SERVER["REQUEST_URI"]));
	}
		
} 