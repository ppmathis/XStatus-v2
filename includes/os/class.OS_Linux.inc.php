<?php
	/**
	 * Linux OS class (extends BaseOS)
	 *
	 * @category 	PHP
	 * @package 	XStatus\OS
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class OS_Linux extends BaseOS {
		
		/**
		 * Constructor
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * [Collect data] Hostname
		 */
		private function _hostname() {
			if(defined('XSTATUS_USE_VHOST') && XSTATUS_USE_VHOST) {
				/* Get vhost hostname */
				$this->_server->hostname = getenv('SERVER_NAME');
			} else {
				/* Get real hostname */
				$result = null;
				if(Functions::readFile('/proc/sys/kernel/hostname', $result, 1)) {
					$result = trim($result);
					$ip = gethostbyname($result);
					if($ip != $result) {
						$this->_server->hostname = gethostbyaddr($ip);
					} else {
						$this->_server->hostname = 'Unknown';
					}
				}
			}
		}
		
		/**
		 * [Collect data] IP
		 */
		private function _ip() {
			if(empty($this->_server->hostname)) $this->_hostname();
			if(defined('XSTATUS_USE_VHOST') && XSTATUS_USE_VHOST) {
				$this->_server->ip = gethostbyname($this->_server->hostname);
			} else {
				$result = $_SERVER['SERVER_ADDR'];
				if(!$result) {
					$this->_server->ip = gethostbyname($this->_server->hostname);
				} else {
					$this->_server->ip = $result;
				}
			}
		}
		
		/**
		 * [Collect data] Kernel version
		 */
		private function _kernel() {
			$buffer = '';
			if(Functions::executeProgram('uname', '-r', $buffer, XSTATUS_DEBUG)) {
				$result = trim($buffer);
				
				/* Detect SMP */
				if(Functions::executeProgram('uname', '-v', $buffer, XSTATUS_DEBUG)) {
					if(preg_match('~SMP~', $buffer)) {
						$result .= ' (SMP)';
					}
				}
				
				/* Detect architecture */
				if(Functions::executeProgram('uname', '-m', $buffer, XSTATUS_DEBUG)) {
					$result .= ' ' . trim($buffer);
				}
				
				$this->_server->kernel = $result;
			} else {
				if(Functions::readFile('/proc/version', $buffer, 1)) {
					if(preg_match('~version (.*?) ~', $buffer, $arrayBuffer)) {
						$result = $arrayBuffer[1];
						
						/* Detect SMP */
						if(preg_match('~SMP~', $buffer)) {
							$result .= ' (SMP)';
						}
						
						$this->_server->kernel = $result;
					}
				}
			}
		}
		
		/**
		 * [Collect data] Distribution
		 */
		private function _distro() {
			/* Parse distribution data file */
			$distroData = @parse_ini_file(XSTATUS_APP_ROOT . '/data/distros.ini', true);
			if(!$distroData) {
				return;
			}
			
			$buffer = '';
			if(Functions::executeProgram('lsb_release', '-a 2>/dev/null', $buffer, XSTATUS_DEBUG)) {
				$tmpDistro = preg_split("~\n~", $buffer, -1, PREG_SPLIT_NO_EMPTY);
				
				/* Get distribution */
				foreach($tmpDistro as $info) {
					$tmpInfo = preg_split('~:~', $info, 2);
					$distro[$tmpInfo[0]] = trim($tmpInfo[1]);
					if(isset($distro['Distributor ID']) && isset($distroData[$distro['Distributor ID']]['Image'])) {
						$this->_server->distro_icon = $distroData[$distro['Distributor ID']]['Image'];
					}
					if(isset($distro['Description'])) {
						$this->_server->distro = $distro['Description'];
					}
				}
			}
		}
		
		/**
		 * [Collect data] Number of users
		 */
		private function _users() {
			$buffer = '';
			if(Functions::executeProgram('who', '-q', $buffer, XSTATUS_DEBUG)) {
				$arrayWho = preg_split('~=~', $buffer);
				$this->_server->users = $arrayWho[1];
			}
		}
		
		/**
		 * [Collect data] Load average
		 */
		private function _loadavg() {
			$buffer = '';
			if(Functions::readFile('/proc/loadavg', $buffer)) {
				$result = preg_split('~\s~', $buffer, 4);
				unset($result[3]);
				$this->_server->loadavg = implode(' ', $result);
			}
		}
		
		/**
		 * [Collect data] Uptime
		 */
		private function _uptime() {
			$buffer = '';
			if(Functions::readFile('/proc/uptime', $buffer, 1)) {
				$arrayBuffer = preg_split('~ ~', $buffer);
				$this->_server->uptime = (int) $arrayBuffer[0];
			}
		}
		
		/**
		 * [Collect data] Software versions
		 */
		private function _software() {
			/* Get generic informations */
			$this->_server->webserver_version = $_SERVER['SERVER_SOFTWARE'];
			$this->_server->php_version = phpversion() . ' with Zend Engine ' . zend_version();
			$this->_server->php_sapi = php_sapi_name();
			$this->_server->php_extensions = count(get_loaded_extensions()) . ' extensions loaded';
			
			/* Get GD version */
			if(function_exists('gd_info')) {
				$tmp = gd_info();
				$this->_server->php_gd_version = $tmp['GD Version'];
			} else {
				$this->_server->php_gd_version = 'N/A';
			}
			
			/* Get MySQL client version */
			if(function_exists('mysql_get_client_info')) {
				preg_match('~mysqlnd [1-9].[0-9].[1-9][0-9]~', mysql_get_client_info(), $tmp);
				$this->_server->mysql_version = $tmp[0];
			} else {
				$this->_server->mysql_version = 'N/A';
			}
		}
		
		/**
		 * [Collect data] Memory usage
		 */
		private function _memory() {
			$buffer = '';
			$tmp = '';
			if(Functions::readFile('/proc/meminfo', $buffer)) {
				/* Parse file */
				$lines = preg_split("~\n~", $buffer, -1, PREG_SPLIT_NO_EMPTY);
				foreach($lines as $line) {
					if(preg_match('~^MemTotal:\s+(.*)\s*kB~i', $line, $tmp)) {
						$this->_server->setMemory('total', $tmp[1] * 1024);
					} else if(preg_match('~^MemFree:\s+(.*)\s*kB~i', $line, $tmp)) {
						$this->_server->setMemory('free', $tmp[1] * 1024);
					} else if(preg_match('~^Cached:\s+(.*)\s*kB~i', $line, $tmp)) {
						$this->_server->setMemory('cache', $tmp[1] * 1024);
					} else if(preg_match('~^Buffers:\s+(.*)\s*kB~i', $line, $tmp)) {
						$this->_server->setMemory('buffer', $tmp[1] * 1024);
					}
				}
				
				/* Calculate total memory usage and swap */
				$this->_server->setMemory('used', $this->_server->memory['total'] - $this->_server->memory['free']);
				if(Functions::readFile('/proc/swaps', $buffer)) {
					$lines = preg_split("~\n~", $buffer, -1, PREG_SPLIT_NO_EMPTY);
					unset($lines[0]);
					foreach($lines as $line) {
						$tmp = preg_split('~\s+~', $line, 5);
						$device = new DiskDevice();
						$device->mount = $tmp[0];
						$device->name = "SWAP";
						$device->total = $tmp[2] * 1024;
						$device->used = $tmp[3] * 1024;
						$device->free = $device->total - $device->used;
						$this->_server->addSwapDevice($device);
					}
				}
			}
		}
		
		/**
		 * [Collect data] Process monitor
		 */
		private function _processes() {
			$buffer = '';
			if(Functions::executeProgram('ps', 'aux', $buffer, XSTATUS_DEBUG)) {
				$lines = preg_split("~\n~", $buffer, -1, PREG_SPLIT_NO_EMPTY);
				unset($lines[0]);
				foreach($lines as $line) {
					$tmp = preg_split('~\s+~', $line, 5);
					$process = new ProcessInfo();
					$this->_server->addProcessInfo($process);
				}
			}
		}
		
		/**
		 * [Collect data] Filesystems
		 */
		private function _filesystems() {
			$result = Functions::parseDf('-P 2>/dev/null');
			foreach($result as $device) {
				$this->_server->addDiskDevice($device);
			}
		}
		
		/**
		 * Collect all the server data
		 * @return void
		 */
		public function collectData() {
			$this->_hostname();
			$this->_ip();
			$this->_kernel();
			$this->_distro();
			$this->_users();
			$this->_loadavg();
			$this->_uptime();
			$this->_software();
			$this->_memory();
			$this->_processes();
			$this->_filesystems();
		}
		
	}

?>