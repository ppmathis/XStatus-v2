<?php
	/**
	* Stores and delivers information of a process
	*
	* @category 	PHP
	* @package 		XStatus
	* @author 		Pascal Mathis <pmathis@snapserv.net>
	* @copyright 	2012 XStatus
	* @license 		http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
	* @link 		http://www.snapserv.net/xstatus
	*
	* @property	string		$name		Name of the process
	*/
	class ProcessInfo {
	
		/**
		 * Name of the disk device
		 * @var string
		 */
		private $_name;
	
		/**
		 * Available getters
		 * @var array
		 */
		private $_getters = array(
				'_name'
		);
	
		/**
		 * Available setters
		 * @var array
		*/
		private $_setters = array(
				'_name'
		);
	
		/**
		 * Getter function (called by PHP)
		 * @param 	string 	$property Property name
		 * @return 	string	 Property value
		*/
		public function __get($property) {
			/* Add prefix */
			$property = '_' . $property;
			if (in_array($property, $this->_getters)) {
				/* Property exists, return it */
				return $this->$property;
			} else if (method_exists($this, '_get' . $property)) {
				/* Custom getter exists, call it */
				return call_user_func(array($this, '_get' . $property));
			} else if (in_array($property, $this->_setters) || method_exists($this, '_set' . $property)) {
				/* Write-only property */
				throw new Exception('Property ' . $property . ' is write-only.');
			} else {
				/* Not accessible */
				throw new Exception('Property ' . $property . ' is not accessible.');
			}
		}
	
		/**
		 * Setter function (called by PHP)
		 * @param string $property Property name
		 * @param string $value Property value
		 * @return void
		 */
		public function __set($property, $value) {
			/* Add prefix */
			$property = '_' . $property;
			if (in_array($property, $this->_setters)) {
				/* Property exists, set it */
				$this->$property = $value;
			} else if (method_exists($this, '_set' . $property)) {
				/* Custom setter exists, call it */
				call_user_func(array($this, '_set' . $property, $value));
			} else if (in_array($property, $this->_getters) || method_exists($this, '_get' . $property)) {
				/* Read-only property */
				throw new Exception('Property ' . $property . ' is read-only.');
			} else {
				/* Write-only property */
				throw new Exception('Property ' . $property . ' is not accessible.');
			}
		}
	
	}
?>