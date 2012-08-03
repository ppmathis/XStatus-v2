<?php 
	/**
	 * Includes
	 */
	require_once('includes/os/interface.BaseOS.inc.php');

	/**
	 * Base class for different operating systems
	 *
	 * @category 	PHP
	 * @package 	XStatus\OS
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */
	abstract class BaseOS implements Interface_BaseOS {
		
		/**
		 * Server object
		 * @var System
		 */
		protected $_server;
		
		/**
		 * Constructor
		 */
		public function __construct() {
			/* Initialize classes */
			$this->_server = new Server();
		}
		
		/**
		 * Gets a filled Server object
		 * @return Server Server object with collected data
		 */
		public function getServer() {
			$this->collectData();
			return $this->_server;
		}
	}
	
?>