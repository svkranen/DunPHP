<?php

namespace DunPHP\Base;

require_once(__DIR__.'/../autoload.php');

class ViewController 
{
	
	public $Controller;
	public $Layout = 'Default';
	private $Resource;
	
	public function __construct($ControllerClass, $layout = 'Default') {
		$tmpController = substr($ControllerClass,strrpos($ControllerClass,'\\')+1);
		$this->Controller = substr($tmpController,0,strrpos($tmpController,'Controller'));
		$this->Resource = new \DunPHP\Base\ResourceController();
		return $this;
	}
	
	public function parseForEach($template) {
		$result = $template;
		
		#Improvement: Check if Count(TagsOpen) = Count(TagsClosed)
		# handle encapsulation!
		
		#Get an array of all Open and Close Tags in Order
		preg_match_all('/\<(f|\/f)\:for/', $template, $output_array);
		
		return $result;
	}
	
	public function parseControllerAction($template) {
		$result = $template;
		$result = preg_replace('/<f:ca \/>/s', $this->Resource->getControllerAction(), $result);
		
		return $result;
	}
	
	public function parseInternalLinks($template) {
		$result = $template;
		preg_match_all('/<f:ilink>\'(\S+)\'<\/f:ilink>/s', $result, $aILinks);
		foreach($aILinks[1] as $iLink) {
			$searchStr = $iLink;
			$searchStr = str_replace('/','\/',$searchStr);
			$searchStr = str_replace('.','\.',$searchStr);
			$strILink = $this->Resource->getILink($iLink);
			$result = preg_replace('/\<f\:ilink\>\\\''.$searchStr.'\'\<\/f\:ilink\>/s', $strILink, $result);
		}
		
		return $result;
	}
	
	public function parseFiles($template) {
		
		#Improvement: Check if Count(TagsOpen) = Count(TagsClosed)
		
		# Function 1: <f:file>'path/file'</f:file>
		$result = $template;
		preg_match_all('/<f:file>\'(\S+)\'<\/f:file>/s', $result, $aFiles);
		foreach($aFiles[1] as $File) {
			$searchStr = $File;
			$searchStr = str_replace('/','\/',$searchStr);
			$searchStr = str_replace('.','\.',$searchStr);
			$strFile = $this->Resource->getPublicFile($File);
			$result = preg_replace('/\<f\:file\>\\\''.$searchStr.'\'\<\/f\:file\>/s', $strFile, $result);
		}
		
		return $result;
	}
	
	public function parseSections($template,$flavour) {
		$layout = file_get_contents($this->Resource->getPrivateFile('Layout/'.$this->Layout.'/'.$flavour.'.tpl'));
		$result = $layout;
		
		#Improvement: Check if Count(TagsOpen) = Count(TagsClosed)
		
		# read different sections from layout (What sections are available?)
		# look for all <sec:name> </sec:name> in layout.tpl
		
		preg_match_all('/\<sec\:([a-zA-Z|\s|\w]*)\>/', $result, $aSections);
		
		# go through all sections and find corresponding substitutes in the template embraced with
		# <sec:name> </sec:name>
		
		foreach ($aSections[1] as $Section) {			
			preg_match_all('/\<sec\:'.$Section.'\>(.*|\s*|\S*|\w*|\W*|\D*|\d*)\<\/sec\:'.$Section.'\>/s', $template, $aSubstitute);
			
			if ($aSubstitute != NULL) {
				$strSubstitute = $aSubstitute[1][0];
			
				/* echo '<br /><ul>';
				echo '<li>'.$Section.'</li>';
				echo '<li>'.$strSubstitute.'</li>'; */
			
				$result = preg_replace('/\<sec\:'.$Section.'\>(.*|\s*|\S*|\W*|\w*|\D*)\<\/sec\:'.$Section.'\>/', '$1'.$strSubstitute, $result);
			}			
		}
		
		# parse different functions
		return $result;
	}
	
	public function getTemplate($action,$flavour = 'default') {
		$template = file_get_contents($this->Resource->getPrivateFile('Template/'.$this->Controller.'/'.$action.'.tpl'));
		
		$template = $this->parseSections($template,$flavour);
		
		#Improvement: Check for <f:...></f:...> - Statements
		$template = $this->parseFiles($template);
		$template = $this->parseInternalLinks($template);
		$template = $this->parseControllerAction($template);
		// $template = $this->parseForEach($template); // not yet ready!
		return $template;
	}
		
}