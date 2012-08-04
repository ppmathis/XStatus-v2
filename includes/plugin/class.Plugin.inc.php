<?php
	/**
	 * XStatus Plugin class
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	abstract class Plugin implements Interface_Plugin {
		
		/**
		 * Get language data
		 * @param	string	$pluginName	Internal plugin name
		 * @return	array	Collected language data
		 */
		protected function _getLanguageData($pluginName) {
			/* Load language file if available */
			$languageFile = XSTATUS_ROOT . '/language/en/plugins/Plugin_' . $pluginName . '.inc.php';
			if(file_exists($languageFile)) {
				require_once($languageFile);
				$languageData = $language;
				unset($language);
				return $languageData;
			} else {
				return array();
			}
		}
		
	}
?>