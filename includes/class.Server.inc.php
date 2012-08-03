<?php 
	/**
	 * Stores and delivers the collected server data
	 *
	 * @category 	PHP
	 * @package 	XStatus
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
	 * @link 		http://www.snapserv.net/xstatus
	 * 
	 * @property	string		$hostname				Hostname
	 * @property	string		$ip						IP address
	 * @property	string		$kernel					Kernel version
	 * @property	string		$distro					Distribution
	 * @property	string		$distro_icon			Distribution icon
	 * @property	int			$users					Active session count
	 * @property	string		$loadavg				Load average
	 * @property	int			$uptime					Server uptime
	 * @property	string		$webserver_version		Webserver version
	 * @property	string		$php_version			PHP version
	 * @property	string		$php_sapi				PHP SAPI
	 * @property	string		$php_extensions			PHP extension count
	 * @property	string		$php_gd_version			PHP GD version
	 * @property	string		$mysql_version			MySQL version
	 * @property	array		$memory					Memory information
	 * @property	array		$swaps					Swap devices
	 * @property	array		$processes				Process list
	 * @property	array		$mounts					Filesystems
	 */

	class Server {
		
		/**
		 * Server hostname
		 * @var string
		 */
		private $_hostname;
		
		/**
		 * Server IP address
		 * @var string
		 */
		private $_ip;
		
		/**
		 * Kernel version
		 * @var string
		 */
		private $_kernel;
		
		/**
		 * Distribution
		 * @var string
		 */
		private $_distro;
		
		/**
		 * Distribution icon
		 * @var string
		 */
		private $_distro_icon;
		
		/**
		 * User count
		 * @var int
		 */
		private $_users;
		
		/**
		 * Server load
		 * @var string
		 */
		private $_loadavg;
		
		/**
		 * Server uptime
		 * @var float
		 */
		private $_uptime;
		
		/**
		 * Webserver version
		 * @var string
		 */
		private $_webserver_version;
		
		/**
		 * PHP version
		 * @var string
		 */
		private $_php_version;
		
		/**
		 * PHP SAPI
		 * @var string
		 */
		private $_php_sapi;
		
		/**
		 * PHP extension count
		 * @var string
		 */
		private $_php_extensions;
		
		/**
		 * PHP GD version
		 * @var string
		 */
		private $_php_gd_version;
		
		/**
		 * MySQL version
		 * @var string
		 */
		private $_mysql_version;
		
		/**
		 * Memory information
		 */
		private $_memory = array();
		
		/**
		 * Swap devices
		 */
		private $_swaps = array();
		
		/**
		 * Processes
		 */
		private $_processes = array();
		
		/**
		 * Filesystems
		 */
		private $_mounts = array();
		
		/**
		 * Available getters
		 * @var array
		 */
		private $_getters = array(
				'_hostname', '_ip', '_kernel', '_distro', '_distro_icon',
				'_users', '_loadavg', '_uptime', '_webserver_version', '_php_version',
				'_php_sapi', '_php_extensions', '_php_gd_version', '_mysql_version', '_memory',
				'_swaps', '_processes', '_mounts'
		);
		
		/**
		 * Available setters
		 * @var array
		 */
		private $_setters = array(
				'_hostname', '_ip', '_kernel', '_distro', '_distro_icon',
				'_users', '_loadavg', '_uptime', '_webserver_version', '_php_version',
				'_php_sapi', '_php_extensions', '_php_gd_version', '_mysql_version'
		);
		
		/**
		 * Getter function (called by PHP)
		 * @param 	string 	$property Property name
		 * @return 	string	 Property value
		 */
		public function __get($property) {
			/* Add prefix */
			$property = '_' . $property;
			if (in_array($property, $this->_getters)) {
				/* Property exists, return it */
				return $this->$property;
			} else if (method_exists($this, '_get' . $property)) {
				/* Custom getter exists, call it */
				return call_user_func(array($this, '_get' . $property));
			} else if (in_array($property, $this->_setters) || method_exists($this, '_set' . $property)) {
				/* Write-only property */
				throw new Exception('Property ' . $property . ' is write-only.');
			} else {
				/* Not accessible */
				throw new Exception('Property ' . $property . ' is not accessible.');
			}
		}
		
		/**
		 * Setter function (called by PHP)
		 * @param string $property Property name
		 * @param string $value Property value
		 * @return void
		 */
		public function __set($property, $value) {
			/* Add prefix */
			$property = '_' . $property;
			if (in_array($property, $this->_setters)) {
				/* Property exists, set it */
				$this->$property = $value;
			} else if (method_exists($this, '_set' . $property)) {
				/* Custom setter exists, call it */
				call_user_func(array($this, '_set' . $property, $value));
			} else if (in_array($property, $this->_getters) || method_exists($this, '_get' . $property)) {
				/* Read-only property */
				throw new Exception('Property ' . $property . ' is read-only.');
			} else {
				/* Write-only property */
				throw new Exception('Property ' . $property . ' is not accessible.');
			}
		}
		
		/**
		 * [Setter] Memory information
		 * @param string	$key	Array key
		 * @param string	$value	Array value
		 * @return void
		 */
		public function setMemory($key, $value) {
			$allowedKeys = array('free', 'total', 'used', 'cache', 'buffer');
			if(in_array($key, $allowedKeys)) {
				$this->_memory[$key] = $value;
			} else {
				/* Not accessible */
				throw new Exception('Property _memory[\'' . $key . '\'] is not accessible.');
			}	
		}
		
		/**
		 * Add a swap device
		 * @param DiskDevice	$swap	Instance of DiskDevice
		 * @return void
		 */
		public function addSwapDevice($swap) {
			array_push($this->_swaps, $swap);
		}
		
		/**
		 * Add a process info
		 * @param ProcessInfo	$process	Instance of ProcessInfo
		 * @return void
		 */
		public function addProcessInfo($process) {
			array_push($this->_processes, $process);
		}
		
		/**
		 * Add a disk device
		 * @param DiskDevice	$disk		Instance of DiskDevice
		 * @return void
		 */
		public function addDiskDevice($disk) {
			array_push($this->_mounts, $disk);
		}
		
		/**
		 * Get data array of Server object
		 * @return array Data array
		 */
		public function getArray() {
			/* Calculate special values */
			$users = intval($this->_users) . ($this->_users == 1 ? ' user logged in' : ' users logged in');
			$now = new DateTime();
			$boot = new DateTime();
			$boot->sub(new DateInterval('PT' . $this->_uptime . 'S'));
			$interval = $boot->diff($now);
			$uptime = $interval->format('%a days %h hours %i minutes');
			$lastboot = $boot->format('r');
			
			/* Calculate swap usage */
			$swap = array('used' => 0, 'total' => 0, 'free' => 0, 'drives' => array());
			$swapdrives = array();
			if(count($this->_swaps) > 0) {
				/* Count total usage */
				foreach($this->_swaps as $swapdrive) {
					$swap['used'] += $swapdrive->used;
					$swap['free'] += $swapdrive->free;
					$swap['total'] += $swapdrive->total;
				}
				/* Count single swap drive usage */
				foreach($this->_swaps as $swapdrive) {	
					$swap['drives'][] = array(
							'mount' => $swapdrive->mount,
							'used' => Functions::convertBytes($swapdrive->used, 2),
							'percent' => number_format($swapdrive->used / $swap['total'] * 100, 2)
					);
				}
			}
			$swap = array(
					'used' => Functions::convertBytes($swap['used'], 2),
					'free' => Functions::convertBytes($swap['free'], 2),
					'total' => Functions::convertBytes($swap['total'], 2),
					'percent' => number_format($swap['used'] / $swap['total'] * 100, 2),
					'drives' => $swap['drives']
			);
			
			/* Calculate memory usage */
			if(!isset($this->_memory['buffer'])) { $this->_memory['buffer'] = 0; }
			if(!isset($this->_memory['cache'])) { $this->_memory['cache'] = 0; }
			$kernel = $this->_memory['used'] - $this->_memory['buffer'] - $this->_memory['cache'];
			$memory = array(
					'total' => Functions::convertBytes($this->_memory['total'], 2),
					'free' => Functions::convertBytes($this->_memory['free'], 2),
					'used' => Functions::convertBytes($this->_memory['used'], 2),
					'cache' => Functions::convertBytes($this->_memory['cache'], 2),
					'buffer' => Functions::convertBytes($this->_memory['buffer'], 2),
					'kernel' => Functions::convertBytes($kernel, 2),
					
					'percent_total' => number_format($this->_memory['used'] / $this->_memory['total'] * 100, 2),
					'percent_kernel' => number_format($kernel / $this->_memory['total'] * 100, 2),
					'percent_cache' => number_format($this->_memory['cache'] / $this->_memory['total'] * 100, 2),
					'percent_buffer' => number_format($this->_memory['buffer'] / $this->_memory['total'] * 100, 2),
			);
			
			/* Processes */
			$processlist = array();
			foreach($this->_processes as $process) {
				$processlist[] = array(
						'name' => $process->name	
				);
			}
			
			/* Filesystems */
			$mounts = array();
			foreach($this->_mounts as $mount) {
				$mounts[] = array(
						'point' => $mount->name,
						'type' => $mount->type,
						'partition' => $mount->options,
						'percent' => number_format($mount->used / $mount->total * 100, 2),
						'size' => Functions::convertBytes($mount->total, 2),
						'used' => Functions::convertBytes($mount->used, 2),
						'free' => Functions::convertBytes($mount->free, 2) 		
				);
			}
			
			/* Return array */
			return array(
					'hostname' => $this->_hostname,
					'ip' => $this->_ip,
					'kernel' => $this->_kernel,
					'distro' => array(
							'icon' => $this->_distro_icon,
							'name' => $this->_distro
					), 'users' => $users,
					'loadavg' => $this->_loadavg,
					'uptime' => $uptime,
					'lastboot' => $lastboot,
					
					'webserver_version' => $this->_webserver_version,
					'php_version' => $this->_php_version,
					'php_sapi' => $this->_php_sapi,
					'php_extensions' => $this->_php_extensions,
					'php_gd_version' => $this->_php_gd_version,
					'mysql_version' => $this->_mysql_version,
					'xstatus_version' => XSTATUS_VERSION,
					
					'memory' => $memory,
					'swap' => $swap,
					'processes' => $processlist,
					'mounts' => $mounts
			);
		}
		
	}
?>