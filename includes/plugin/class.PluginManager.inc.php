<?php
	/**
	 * PluginManager class
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class PluginManager {
		
		/**
		 * Singleton instance
		 * @var PluginManager
		 */
		private static $_instance = null;
		
		/**
		 * Plugin list
		 * @var array
		 */
		private $_pluginList = array();
		
		/**
		 * Returns a singleton instance
		 * @return PluginManager Singleton instance
		 */
		public static function getInstance() {
			if(!self::$_instance) {
				self::$_instance = new PluginManager();
			}
			return self::$_instance;
		}
		
		/**
		 * Load a new plugin
		 * @param 	string 	$name	Plugin name
		 * @return	void
		 */
		public function loadPlugin($name) {
			/* Create new instance and add to plugin list */
			$className = 'Plugin_' . $name;
			$instance = new $className();
			$this->_pluginList[] = $instance;
		}
		
		/**
		 * Get all plugins
		 * @return array Array of all loaded plugins
		 */
		public function getPlugins() {
			return $this->_pluginList;
		}
		
	}
?>