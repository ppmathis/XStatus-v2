<?php
	/**
	 * XStatus Class autoloader
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	/**
	 * Automatic loading of used classes
	 * @param	string	Name of the class which must be loaded
	 * @return	void
	 */
	function __autoload($className) {
		/* Build include name */
		$includeName = 'class.' . $className . '.inc.php';
		
		/* Available autoload directories */
		$directories = array(
				'/includes/',
				'/includes/plugin/',
				'/includes/xml/',
				'/plugins/'	
		);
		
		/* Search through directories */
		foreach($directories as $directory) {
			if(file_exists(XSTATUS_ROOT . $directory . $includeName)) {
				require_once(XSTATUS_ROOT . $directory . $includeName);
				return;				
			}
		}
		
		/* Generate a fatal error */
		$error = Error::getInstance();
		$error->addError('_autoload("' . $className . '")', 'Autoloading of class "' . $className . '" failed.');
		$error->showXML();
	}
?>