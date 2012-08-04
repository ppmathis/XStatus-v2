<?php
	/**
	 * XStatus Base plugin - Language file
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	global $language;
	$language = array(
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
			)
	);
?>