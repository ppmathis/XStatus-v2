<?php
	/**
	 * Error class
	 *
	 * @category 	PHP
	 * @package 	XStatus
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class Error {
		
		/**
		 * Singleton instance
		 * @var Error
		 */
		private static $_instance = null;
		
		/**
		 * Error list
		 * @var array
		 */
		private $_errorList = array();
		
		/**
		 * Returns a singleton instance
		 * @return Error Singleton instance
		 */
		public static function getInstance() {
			if(!self::$_instance) {
				self::$_instance = new Error();
			}
			return self::$_instance;
		}
		
		/**
		 * Adds an error
		 * @param	string	$command		The command which failed
		 * @param	string	$description	Error description
		 * @return	void
		 */
		public function addError($command, $description) {
			$this->_errorList[] = array(
					'command' => $command,
					'message' => $description	
			);
		}
		
		/**
		 * Shows all errors as an XML
		 * @return void
		 */
		public function showXML() {
			/* Create DOM document */
			$dom = new DOMDocument('1.0', 'UTF-8');
			$root = $dom->createElement('XStatus');
			$dom->appendChild($root);
			
			/* Generate XML */
			$xml = new SimpleXMLExtended(simplexml_import_dom($dom), 'UTF-8');
			$generation = $xml->addChild('Generation');
			$generation->addAttribute('version', XSTATUS_VERSION);
			$generation->addAttribute('timestamp', time());
			$errors = $xml->addChild('Errors');
			foreach($this->_errorList as $error) {
				$errorNode = $errors->addCData('Error', $error['message']);
				$errorNode->addAttribute('Function', $error['command']);
			}
			
			/* Return XML */
			header('Cache-Control: no-cache, must-revalidate');
			header('Content-Type: text/xml');
			echo $xml->getSimpleXMLElement()->asXML();
			exit();
		}
	}
?>