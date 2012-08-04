<?php
	/**
	 * SimpleXMLExtended class
	 *
	 * @category 	PHP
	 * @package 	XStatus\XML
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class SimpleXMLExtended {

		/**
		 * Stores the actual encoding
		 * @var string
		 */
		private $_encoding = null;
		
		/**
		 * SimpleXmlElement instance to work with
		 * @var SimpleXMLElement
		 */
		private $_simpleXMLElement = null;
		
		/**
		 * Class constructor
		 * @param	SimpleXMLElement	$xml		Instance to work with
		 * @param	string				$encoding	Base encoding
		 * @return	void
		 */
		public function __construct($xml, $encoding) {
			/* Set class properties */
			$this->_encoding = $encoding;
			$this->_simpleXMLElement = $xml;
		}
		
		/**
		 * Insert a child element with or without a value
		 * @param	string	$name		Name of the child element
		 * @param	string	$value		Value of the child element
		 * @return	SimpleXMLExtended	Extended child element
		 */
		public function addChild($name, $value = null) {
			$encodedName = $this->_toUTF8($name);
			if($value == null) {
				return new SimpleXMLExtended($this->_simpleXMLElement->addChild($encodedName), $this->_encoding);
			} else {
				$encodedValue = $this->_toUTF8($value);
				return new SimpleXMLExtended($this->_simpleXMLElement->addChild($encodedName, $encodedValue), $this->_encoding);
			}
		}
		
		/**
		 * Insert a child with CDATA section
		 * @param	string	$name		Name of the child element
		 * @param	string	$cdata		Data for CDATA section
		 * @return	SimpleXMLExtended	Extended child element
		 */
		public function addCData($name, $cdata) {
			$encodedName = $this->_toUTF8($name);
			$node = $this->_simpleXMLElement->addChild($encodedName);
			$domNode = dom_import_simplexml($node);
			$no = $domNode->ownerDocument;
			$domNode->appendChild($no->createCDATASection($cdata));
			return new SimpleXMLExtended($node, $this->_encoding);
		}
		
		/**
		 * Add an attribute to a child
		 * @param	string	$name	Name of the attribute
		 * @param	value	$value	Value of the attribute
		 * @return	void
		 */
		public function addAttribute($name, $value) {
			$encodedName = $this->_toUTF8($name);
			$encodedValue = htmlspecialchars($this->_toUTF8($value));
			$this->_simpleXMLElement->addAttribute($encodedName, $encodedValue);
		}
		
		/**
		 * Converts a string into an UTF-8 string
		 * @param 	string	$string	String to convert
		 * @return	string 	UTF-8 string
		 */
		private function _toUTF8($string) {
			if($this->_encoding != null) {
				$encList = mb_list_encodings();
				if(in_array($this->_encoding, $encList)) {
					return mb_convert_encoding(trim($string), 'UTF-8', $this->_encoding);
				} else if(function_exists('iconv')) {
					return iconv($this->_encoding, 'UTF-8', trim($string));
				} else {
					return mb_convert_encoding(trim($string), 'UTF-8');
				}
			} else {
				return mb_convert_encoding(trim($string), 'UTF-8');
			}
		}
		
		/**
		 * Returns the SimpleXMLElement
		 * @return SimpleXMLElement Instance of SimpleXMLElement
		 */
		public function getSimpleXMLElement() {
			return $this->_simpleXMLElement;
		}
	}
?>