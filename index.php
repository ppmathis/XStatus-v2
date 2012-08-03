<?php
	/**
	 *  XStatus
	 *  Fast and lightweight server status script
	 *  
	 *  @category 		PHP
	 *  @package 		XStatus
	 *  @author 		Pascal Mathis <pmathis@snapserv.net>
	 *  @copyright 		2012 XStatus
	 *  @license 		http://www.snapserv.net/xstatus_license Custom license
	 *  @link 			http://www.snapserv.net/xstatus
	 */

	/* Supports clean code style */
	error_reporting(E_ALL);
	
	/* Configuration */
	define('XSTATUS_VERSION', 'v2.0.1 beta (public-testing)');
	define('XSTATUS_USE_VHOST', false);
	define('XSTATUS_DEBUG', false);
	define('XSTATUS_APP_ROOT', '/home/pmathis/www/snapserv.net/public_html/statusv2');
	define('XSTATUS_SHOW_MOUNT_OPTION', true);
	define('XSTATUS_SHOW_MOUNT_CREDENTIALS', false);
	
	/* System includes */
	require_once('includes/class.Error.inc.php');
	require_once('includes/class.Server.inc.php');
	require_once('includes/class.ProcessInfo.inc.php');
	require_once('includes/devices/class.DiskDevice.inc.php');
	require_once('includes/devices/class.CpuDevice.inc.php');
	require_once('includes/class.Functions.inc.php');
	require_once('includes/os/interface.BaseOS.inc.php');
	require_once('includes/os/class.BaseOS.inc.php');
	require_once('includes/class.OSLoader.inc.php');
	require_once('includes/class.Template.inc.php');
	
	/* Example */
	$loader = new OSLoader();
	$server = $loader->getOS()->getServer();

	/* Language data */
	$lang = array(
			'server_overview' => 'Server overview',
			'software_overview' => 'Software overview',
			'memory_usage' => 'Memory usage',
			'cpu_usage' => 'Processor usage',
			
			'hostname' => 'Canonical hostname',
			'ip' => 'Listening IP',
			'kernel' => 'Kernel version',
			'distro' => 'Distribution name',
			'users' => 'Active sessions',
			'loadavg' => 'Load average',
			'uptime' => 'Uptime',
			'lastboot' => 'Last boot',

			'webserver_version' => 'Webserver',
			'php_version' => 'PHP version',
			'php_sapi' => 'PHP SAPI',
			'php_extensions' => 'PHP extension count',
			'php_gd_version' => 'PHP GD version',
			'mysql_version' => 'MySQL version',
			'xstatus_version' => 'XStatus version',
			
			'memory' => array(
					'type' => 'Type',
					'usage' => 'Usage',
					'free' => 'Free',
					'used' => 'Used',
					'size' => 'Size',
					'physical' => 'Physical memory',
					'kernel' => 'Kernel and applications',
					'buffer' => 'Buffers',
					'cache' => 'Cached',
					'swap' => 'Disk swap',		
			),
			
			'mounts' => 'Mounted filesystems',
			'mount' => array(
					'point' => 'Mountpoint',
					'type' => 'Type',
					'partition' => 'Partition',
					'usage' => 'Usage',
					'free' => 'Free',
					'used' => 'Used',
					'size' => 'Size'
			),
	);
	
	/* Template test */
	$page = new Template('templates/default/overview.tpl');
	$page->assign(array(
			'tplpath' => 'templates/default',
			'gfxpath' => 'templates/gfx',
			'data' => $server->getArray(),
			'lang' => $lang
	));
	$page->display();
?>