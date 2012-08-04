<?php
	/**
	 * XStatus Base plugin
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class Plugin_Base extends Plugin {
		
		/**
		 * Plugin properties
		 */
		const PLUGIN_NAME = 'XStatus Base Plugin';
		const PLUGIN_SYS_NAME = 'Base';
		const PLUGIN_LANG_NAME = 'Base';
		const PLUGIN_OUT_NAME = 'base';
		
		/**
		 * Collected data array
		 */
		private $_data = array();
		
		/**
		 * Plugin constructor
		 */
		public function __construct() {
			/* Detect server OS */
			if(defined('XSTATUS_OS')) {
				$detectedOS = XSTATUS_OS;
			} else {
				$detectedOS = php_uname('s');
			}
			
			/* Load the correct plugin */
			switch($detectedOS) {
				case 'Linux':
					$manager = PluginManager::getInstance();
					$manager->loadPlugin('Base_Linux');
					break;
					
				default:
					$error = Error::getInstance();
					$error->addError('new Plugin_Base()', 'Unsupported operating system.');
					$error->showXML();
			}
		}
		
		/**
		 * Get the plugin name
		 * @return string Name of the plugin
		 */
		public function getName() {
			return 'XStatus Base Plugin';
		}
		
		/**
		 * Collect server data
		 * @return void
		 */
		public function collectData() {
			/* OS-specific classes are collecting data */
			return;
		}
		
		/**
		 * Get the collected server data
		 * @param 	bool	$liveData	Only live data?
		 * @return	array	Collected server data
		 */
		public function getData($liveData = false) {
			/* OS-specific classes are returning data */
			return null;
		}
		
	}
?>