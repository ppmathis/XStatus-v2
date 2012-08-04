<?php
	/**
	 * Template class
	 *
	 * @category 	PHP
	 * @package 	XStatus
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class Template {
		
		/**
		 * Stored data
		 * @var array
		 */
		private $_data = array('__alt__' => null);
		
		/**
		 * File buffer
		 * @var string
		 */
		private $_buffer;
		
		/**
		 * Directory where Template file is
		 * @var string
		 */
		private $_directory;
		
		/**
		 * Constructor
		 * @param 	string	$templateFile	Template file to load
		 */
		public function __construct($templateFile) {
			/* Check if file exists and is readable */
			if(!file_exists($templateFile) || !is_readable($templateFile)) {
				$error = Error::getInstance();
				$error->addError('new Template("' . $templateFile . '")', 'Can not load template file "' . $templateFile . '"');
				$error->showXML();
			}
			
			/* Read the complete file into the buffer */
			$this->_buffer = file_get_contents($templateFile);
			$this->_directory = dirname($templateFile);
		}
		
		/**
		 * [Parser] Ignore tag
		 * @param 	array	$input 	Callback input
		 * @return 	string 	Callback output
		 */
		private function _parseIgnore($input) {
			/* Process input */
			$text = $input[1];
			
			/* Encode all the parentheses */
			$text = str_replace('{', '&#123;', $text);
			$text = str_replace('}', '&#125;', $text);
			
			return $text;
		}
		
		/**
		 * [Parser] Normal variable
		 * @param 	array	$input	Callback input
		 * @return 	string 	Callback output
		 */
		private function _parseVariable($input) {
			/* Process input */
			$original = $input[0];
			$varName = $input[1];
			
			/* Support nesting */
			$var = null;
			if(substr_count($varName, '.') > 0) {
				/* Nested variable */
				$parts = explode('.', $varName);
				$tmp = $this->_data;
				foreach($parts as $part) {
					/* Continue nesting if key exists */
					if(array_key_exists($part, $tmp)) {
						$tmp = $tmp[$part];
					} else {
						return $original;
					}
				}
				return $tmp;
			} else {
				/* Normal variable */
				reset($this->_data);
				if(array_key_exists($varName, $this->_data)) {
					return $this->_data[$varName];
				} else {
					return $original;
				}
			}	
		}
		
		/**
		 * [Parser] Loop tag
		 * @param 	array	$input	Callback input
		 * @return 	string 	Callback output
		 */
		private function _parseLoop($input) {
			/* Process input */
			$original = $input[0];
			$varName = $input[1];
			$keyName = $input[2];
			$outName = $input[3];
			$this->_data['__alt__'] = 1 ^ $input[4];
			$data = trim($input[5]);

			/* Support nesting */
			$var = null;
			if(substr_count($varName, '.') > 0) {
				/* Nested variable */
				$parts = explode('.', $varName);
				$tmp = $this->_data;
				foreach($parts as $part) {
					/* Continue nesting if key exists */
					if(array_key_exists($part, $tmp)) {
						$tmp = $tmp[$part];
					} else {
						return $original;
					}
				}
				$var = $tmp;
			} else {
				/* Normal variable */
				if(!array_key_exists($varName, $this->_data) || !is_array($this->_data[$varName])) {
					return $original;
				}
				$var = $this->_data[$varName];
			}
			
			/* Loop through array */
			$result = '';
			$originalData = $this->_data;
			foreach($var as $this->_data[$keyName] => $this->_data[$outName]) {
				$this->_data['__alt__'] = ($this->_data['__alt__'] == '0' ? '1' : '0');
				$result .= $this->_parse($data);
			}
			$this->_data = $originalData;
			unset($originalData);
			
			return $result;
		}
		
		/**
		 * Set the internal data
		 * @param 	array	$data	The new data array
		 * @access private
		 */
		public function setData($data) {
			$this->_data = $data;
		}
		
		/**
		 * [Parser] Include tag
		 * @param 	array	$input	Callback input
		 * @return 	string 	Callback output
		 */
		private function _parseInclude($input) {
			/* Process input */
			$includeFile = $input[1];
			
			/* Create new Template object */
			$tpl = new Template($this->_directory . '/' . $includeFile);
			$tpl->setData($this->_data);
			return $tpl->output();
		}
		
		/**
		 * [Parser] This tag
		 * @param 	array	$input	Callback input
		 * @return	string	Callback output
		 */
		private function _parseThis($input) {
			/* Process input */
			$scopeName = $input[1];
			
			/* Set scope of "this" variable */
			if(array_key_exists('scopes', $this->_data)) {
				if(array_key_exists($scopeName, $this->_data['scopes'])) {
					$this->_data['this'] = &$this->_data['scopes'][$scopeName];
				}
			}
			
			return '';
		}
		
		/**
		 * Parse the template file
		 * @param 	string	$text	Text to parse
		 * @return void
		 */
		private function _parse($text) {
			/* Parse the template */
			$text = preg_replace_callback('~\{ignore\}(.*?)\{/ignore\}~U', array(&$this, '_parseIgnore'), $text);
			$text = preg_replace_callback('~\{this scope=\'(.*?)\'\}~U', array(&$this, '_parseThis'), $text);
			$text = preg_replace_callback('~\{loop in=\'([A-Za-z0-9_\.]*)\' key=\'([A-Za-z0-9_]*)\' out=\'([A-Za-z0-9_]*)\' alt=([01])}(.*)\{/loop\}~sU', array(&$this, '_parseLoop'), $text);
			$text = preg_replace_callback('~\{([A-Za-z0-9_\.]{1,})\}~U', array(&$this, '_parseVariable'), $text);
			$text = preg_replace_callback('~\{include file=\'(.*?)\'\}~U', array(&$this, '_parseInclude'), $text);
		
			/* Revert encoding of parentheses */
			$text = str_replace('&#123;', '{', $text);
			$text = str_replace('&#125;', '}', $text);
			
			return $text;
		}
		
		/**
		 * Assign template variables
		 * @param	array	$data	The template variables in an array
		 * @return 	void
		 */
		public function assign($data) {
			/* Store array */
			foreach($data as $name => $value) {
				$this->_data[$name] = $value;				
			}
		}
		
		/**
		 * Return the page
		 * @return string Page output
		 */
		public function output() {
			/* Parse the template */
			$result = $this->_parse($this->_buffer);
			$result = $this->_parse($result);
			
			/* Compress HTML output (thanks to Alan Moore) */
			$result = preg_replace('#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre|script)\b))*+)(?:<(?>textarea|pre|script)\b|\z))#', '', $result);
			
			return $result;
		}
		
		/**
		 * Display the page
		 * @return void
		 */
		public function display() {
			/* Output the page */
			echo $this->output();
		}
		
	}
?>