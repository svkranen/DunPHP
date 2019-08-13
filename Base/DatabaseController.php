<?php

namespace DunPHP\Base;

require_once(__DIR__.'/../autoload.php');

class DatabaseController
{
	
	private $db_host;
	private $db_port;
	private $db_user;
	private $db_password;
	private $db_name;
	private $connector;
	
	public function __construct() {
		#read access data from config file
		
		$Resource = new \DunPHP\Base\ResourceController();
		
		$config = parse_ini_file($Resource->getConf());
		
		$this->db_host = $config['db_host'];
		$this->db_port = ( $config['db_port'] != '3306' ? $config['db_port'] : '3306');
		$this->db_user = $config['db_user'];
		$this->db_password = $config['db_password'];
		$this->db_name = $config['db_name'];
		
		try {	
			$this->connector = new \PDO(
				'mysql:host='.$this->db_host.';dbport='.$this->db_port.';dbname='.$this->db_name, 
				$this->db_user, 
				$this->db_password, 
				array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
				
		} catch (\PDOException $e) {
			print 'Error, while connecting database: '.$e->getMessage().'<br />';	
			die();
		}
	}
	
	public function getQueryResult($query) {
		$result = $this->connector->query($query);
		
		if ($result) {
			return $result;
		} else {
			return NULL;
		}
	}
	
	public function getConnector() {
		return $this->connector;
	}
	
}