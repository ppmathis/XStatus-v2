<?php
	/**
	 * Interface for class "BaseOS"
	 *
	 * @category 	PHP
	 * @package 	XStatus\OS
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */
	interface Interface_BaseOS {
		
		/**
		 * Collect all the server data
		 * @return void
		 */
		public function collectData();
		
	}
?>