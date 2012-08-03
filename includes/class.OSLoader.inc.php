<?php
	/**
	 * Loads the correct OS class if available
	 *
	 * @category 	PHP
	 * @package 	XStatus\OS
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class OSLoader {
		
		/**
		 * Detected operating system
		 * @var string
		 */
		private $_detectedOS;
		
		/**
		 * Operating system object
		 * @var BaseOS
		 */
		private $_OS;
		
		/**
		 * Constructor
		 */
		public function __construct() {
			/* Check if operating system was predefined */
			if(defined('XSTATUS_OS')) {
				$this->_detectedOS = XSTATUS_OS;
			} else {
				$this->_detectedOS = php_uname('s');
			}
			
			/* Detect the operating system */
			switch($this->_detectedOS) {
				case 'Linux':
					/* Load required includes */
					require_once('os/class.OS_Linux.inc.php');
					$this->_OS = new OS_Linux();
					
					break;
				default:
					die('Unrecognized operating system!');
			}
		}
		
		/**
		 * Get the BaseOS object
		 * @return BaseOS The OS object
		 */
		public function getOS() {
			return $this->_OS;
		}
		
	}
?>