<?php
	/**
	 * XStatus
	 * Fast and lightweight server status script
	 * 
	 * @category		PHP
	 * @package			XStatus
	 * @author			Pascal Mathis <pmathis@snapserv.net>
	 * @copyright		2012 XStatus
	 * @license			http://www.snapserv.net/xstatus_license Custom license
	 * @link 			http://www.snapserv.net/xstatus
	 */
	
	/* Display all errors, even notices */
	error_reporting(E_ALL);
	
	/* Setup autoloader */
	require_once('includes/autoloader.inc.php');
	
	/* Configuration */
	define('XSTATUS_ROOT', dirname(__FILE__));
	define('XSTATUS_VERSION', 'v2.1.1 beta (public-testing)');
	define('XSTATUS_DEBUG', false);
	define('XSTATUS_LANGUAGE', 'en');
	define('XSTATUS_USE_VHOST', false);
	define('XSTATUS_SHOW_MOUNT_OPTION', true);
	define('XSTATUS_SHOW_MOUNT_CREDENTIALS', false);
	define('XSTATUS_REFRESH_RATE', 5000);
	define('XSTATUS_UPDATE_CHECK', false);
	
	$pluginList = array(
			'Base',	
	);
	/* End of configuration */

	/* Load all plugins */
	$manager = PluginManager::getInstance();
	foreach($pluginList as $pluginName) {
		$manager->loadPlugin($pluginName);
	}
	
	/* Collect and store plugin data */
	$tplData = array();
	$liveData = array();
	foreach($manager->getPlugins() as $plugin) {
		$plugin->collectData();
		$pluginData = $plugin->getData(false);
		$pluginLiveData = $plugin->getData(true);
		
		if($pluginData != null)
			$tplData['plugin.' . $pluginData['name']] = $pluginData['data'];
		if($pluginLiveData != null)
			$liveData['plugin.' . $pluginLiveData['name']] = $pluginLiveData['data'];
	}
	
	/* Return JSON data if requested */
	if(isset($_GET['return']) && $_GET['return'] == 'json') {
		echo json_encode($liveData);
		die();
	}
	
	/* Display overview page */
	$page = new Template('templates/default/overview.tpl');
	$page->assign(array(
			'tplpath'	=> 'templates/default',
			'gfxpath'	=> 'templates/gfx',
			'jspath' 	=> 'templates/js',
			'scopes'	=> $tplData,
			'refresh'	=> XSTATUS_REFRESH_RATE
	));
	$page->display();
?>