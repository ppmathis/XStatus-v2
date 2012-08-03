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
		 * @param string	$command		The command which failed
		 * @param string	$description	Error description
		 * @return void
		 */
		public function addError($command, $description) {
			echo '';
		}
		
	}
?>