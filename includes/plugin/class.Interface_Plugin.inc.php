<?php
	/**
	 * Interface for class "Plugin"
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */
	interface Interface_Plugin {
		
		/**
		 * Get the plugin name
		 * @return string Name of the plugin
		 */
		public function getName();
		
		/**
		 * Collect server data
		 * @return void
		 */
		public function collectData();
		
		/**
		 * Get the collected server data
		 * @param 	bool	$liveData	Only live data?
		 * @return	array	Collected server data
		 */
		public function getData($liveData = false);
		
	}
?>