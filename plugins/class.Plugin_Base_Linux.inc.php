<?php
	/**
	 * XStatus Base plugin (Linux)
	 *
	 * @category 	PHP
	 * @package 	XStatus\Plugin
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class Plugin_Base_Linux extends Plugin {
		
		/**
		 * Plugin properties
		 */
		const PLUGIN_NAME = 'XStatus Base Plugin [Linux]';
		const PLUGIN_SYS_NAME = 'Base_Linux';
		const PLUGIN_LANG_NAME = 'Base';
		const PLUGIN_OUT_NAME = 'base'; 
		
		/**
		 * Collected data array
		 */
		private $_data = array();
		
		/**
		 * Get the plugin name
		 * @return string Name of the plugin
		 */
		public function getName() {
			return self::PLUGIN_NAME;
		}
		
		/**
		 * [Collect data] Hostname
		 */
		private function _hostname() {
			if(defined('XSTATUS_USE_VHOST') && XSTATUS_USE_VHOST) {
				/* Get vhost hostname */
				$this->_data['hostname'] = getenv('SERVER_NAME');
			} else {
				/* Get real hostname */
				$result = null;
				if(Functions::readFile('/proc/sys/kernel/hostname', $result, 1)) {
					$result = trim($result);
					$ip = gethostbyname($result);
					if($ip != $result) {
						$this->_data['hostname'] = gethostbyaddr($ip);
					} else {
						$this->_data['hostname'] = 'Unknown';
					}
				}
			}
		}
		
		/**
		 * [Collect data] IP
		 */
		private function _ip() {
			if(empty($this->_data['hostname'])) $this->_hostname();
			if(defined('XSTATUS_USE_VHOST') && XSTATUS_USE_VHOST) {
				$this->_data['ip'] = gethostbyname($this->_data['hostname']);
			} else {
				$result = $_SERVER['SERVER_ADDR'];
				if(!$result) {
					$this->_data['ip'] = gethostbyname($this->_data['hostname']);
				} else {
					$this->_data['ip'] = $result;
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
		
				$this->_data['kernel'] = $result;
			} else {
				if(Functions::readFile('/proc/version', $buffer, 1)) {
					if(preg_match('~version (.*?) ~', $buffer, $arrayBuffer)) {
						$result = $arrayBuffer[1];
		
						/* Detect SMP */
						if(preg_match('~SMP~', $buffer)) {
							$result .= ' (SMP)';
						}
		
						$this->_data['kernel'] = $result;
					}
				}
			}
		}
		
		/**
		 * [Collect data] Distribution
		 */
		private function _distro() {
			/* Parse distribution data file */
			$distroData = @parse_ini_file(XSTATUS_ROOT . '/data/distros.ini', true);
			if(!$distroData) {
				return;
			}
				
			$buffer = '';
			$this->_data['distro'] = array();
			if(Functions::executeProgram('lsb_release', '-a 2>/dev/null', $buffer, XSTATUS_DEBUG)) {
				$tmpDistro = preg_split("~\n~", $buffer, -1, PREG_SPLIT_NO_EMPTY);
		
				/* Get distribution */
				foreach($tmpDistro as $info) {
					$tmpInfo = preg_split('~:~', $info, 2);
					$distro[$tmpInfo[0]] = trim($tmpInfo[1]);
					if(isset($distro['Distributor ID']) && isset($distroData[$distro['Distributor ID']]['Image'])) {
						$this->_data['distro']['icon'] = $distroData[$distro['Distributor ID']]['Image'];
					}
					if(isset($distro['Description'])) {
						$this->_data['distro']['name'] = $distro['Description'];
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
				$userCount = $arrayWho[1];
				
				/* Format data */
				$this->_data['users'] = intval($userCount) . ($userCount == 1 ? ' user logged in' : ' users logged in');
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
				$this->_data['loadavg'] = implode(' ', $result);
			}
		}
		
		/**
		 * [Collect data] Uptime
		 */
		private function _uptime() {
			$buffer = '';
			if(Functions::readFile('/proc/uptime', $buffer, 1)) {
				$arrayBuffer = preg_split('~ ~', $buffer);
				$runningSince = (int) $arrayBuffer[0];
				
				/* Format data */
				$boot = new DateTime();
				$boot->sub(new DateInterval('PT' . $runningSince . 'S'));
				$interval = $boot->diff(new DateTime());
				$this->_data['uptime'] = $interval->format('%a days %h hours %i minutes');
				$this->_data['lastboot'] = $boot->format('r');
			}
		}
		
		/**
		 * [Collect data] Software versions
		 */
		private function _software() {
			/* Get generic informations */
			$this->_data['webserver_version'] = $_SERVER['SERVER_SOFTWARE'];
			$this->_data['php_version'] = phpversion() . ' with Zend Engine ' . zend_version();
			$this->_data['php_sapi'] = php_sapi_name();
			$this->_data['php_extensions'] = count(get_loaded_extensions()) . ' extensions loaded';
				
			/* Get GD version */
			if(function_exists('gd_info')) {
				$tmp = gd_info();
				$this->_data['php_gd_version'] = $tmp['GD Version'];
			} else {
				$this->_data['php_gd_version'] = 'N/A';
			}
				
			/* Get MySQL client version */
			if(function_exists('mysql_get_client_info')) {
				preg_match('~mysqlnd [1-9].[0-9].[1-9][0-9]~', mysql_get_client_info(), $tmp);
				$this->_data['mysql_version'] = $tmp[0];
			} else {
				$this->_data['mysql_version'] = 'N/A';
			}
			if(XSTATUS_UPDATE_CHECK == true) {
				$actualVersion = trim(file_get_contents('http://www.snapserv.net/update/xstatus.txt'));
				if($actualVersion != null && $actualVersion != XSTATUS_VERSION) {
					$updateText = '<a href="http://www.github.com/NeoXiD/XStatus" target="_blank" style="color: #77AA11;">';
					$updateText .= 'New version available</p>';
					$updateText .= '</a>';
					$this->_data['xstatus_version'] = XSTATUS_VERSION . ' - ' . $updateText;
				} else {
					$this->_data['xstatus_version'] = XSTATUS_VERSION;
				}
			} else {
				$this->_data['xstatus_version'] = XSTATUS_VERSION;
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
						$rawTotal = $tmp[1] * 1024;
					} else if(preg_match('~^MemFree:\s+(.*)\s*kB~i', $line, $tmp)) {
						$rawFree = $tmp[1] * 1024;
					} else if(preg_match('~^Cached:\s+(.*)\s*kB~i', $line, $tmp)) {
						$rawCache = $tmp[1] * 1024;
					} else if(preg_match('~^Buffers:\s+(.*)\s*kB~i', $line, $tmp)) {
						$rawBuffer = $tmp[1] * 1024;
					}
				}
		
				/* Calculate kernel and total memory usage */
				$rawUsed = $rawTotal - $rawFree;
				$rawKernel = $rawUsed - $rawCache - $rawBuffer;
				
				/* Get swap usage */
				$swapTotal = $swapUsed = 0;
				if(Functions::readFile('/proc/swaps', $buffer)) {
					$lines = preg_split("~\n~", $buffer, -1, PREG_SPLIT_NO_EMPTY);
					unset($lines[0]);
					
					$count = 0;
					foreach($lines as $line) {
						/* Drive specific swap size */
						$tmp = preg_split('~\s+~', $line, 5);
						$swapDrives[] = array(
								'mount' => $tmp[0],
								'intname' => preg_replace('/[^A-Za-z0-9]/i', '', $tmp[0]) . $count,
								'used' => Functions::convertBytes($tmp[3] * 1024, 2),
								'percent' => number_format(($tmp[3] * 1024) / ($tmp[2] * 1024) * 100, 2)	
						);
						
						/* General swap size */
						$swapTotal += $tmp[2] * 1024;
						$swapUsed += $tmp[3] * 1024;
						$count++;
					}
				}
				$swapFree = $swapTotal - $swapUsed;
				
				/* Format data */
				$this->_data['memory'] = array(
						'total' => Functions::convertBytes($rawTotal, 2),
						'free' => Functions::convertBytes($rawFree, 2),
						'used' => Functions::convertBytes($rawUsed, 2),
						'cache' => Functions::convertBytes($rawCache, 2),
						'buffer' => Functions::convertBytes($rawBuffer, 2),
						'kernel' => Functions::convertBytes($rawKernel, 2),
						
						'percent_total' => number_format($rawUsed / $rawTotal * 100, 2),
						'percent_kernel' => number_format($rawKernel / $rawTotal * 100, 2),
						'percent_cache' => number_format($rawCache / $rawTotal * 100, 2),
						'percent_buffer' => number_format($rawBuffer / $rawTotal * 100, 2),
				);
				$this->_data['swap'] = array(
						'total' => Functions::convertBytes($swapTotal, 2),
						'free' => Functions::convertBytes($swapFree, 2),
						'used' => Functions::convertBytes($swapUsed, 2),
						'percent' => number_format($swapUsed / $swapTotal * 100, 2),
						'drives' => $swapDrives
				);
			}
		}
		
		/**
		 * [Collect data] Filesystems
		 */
		private function _filesystems() {
			$result = Functions::parseDf('-P 2>/dev/null');
			/* Format data */
			$this->_data['mounts'] = array();
			$count = 0;
			foreach($result as $device) {
				$this->_data['mounts'][] = array(
						'point' => $device['name'],
						'intname' => preg_replace('/[^A-Za-z0-9]/i', '', $device['name']) . $count,
						'type' => $device['type'],
						'partition' => $device['options'],
						
						'size' => Functions::convertBytes($device['total'], 2),
						'free' => Functions::convertBytes($device['free'], 2),
						'used' => Functions::convertBytes($device['used'], 2),
						'percent' => number_format($device['used'] / $device['total'] * 100, 2)	
				);
				$count++;
			}
		}
		
		/**
		 * Collect server data
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
			$this->_filesystems();
		}
		
		/**
		 * Get the collected server data
		 * @param 	bool	$liveData	Only live data?
		 * @return	array	Collected server data
		 */
		public function getData($liveData = false) {
			/* Live update keys */
			$updateKeys = array(
					'users', 'loadavg', 'uptime', 'lastboot'	
			);
			
			if($liveData == false) {
				/* Collect language data and return the array */
				$this->_data['lang'] = $this->_getLanguageData(self::PLUGIN_LANG_NAME);
				return array(
						'name' => self::PLUGIN_OUT_NAME,
						'data' => $this->_data
				);
			} else {
				/* Filter data */
				$liveData = $this->_data;
				foreach($liveData as $key => $data) {
					if(!in_array($key, $updateKeys)) {
						unset($liveData[$key]);
					}
				}
				
				/* Add advanced data */
				$liveData['adv'] = array(
						'memory' => $this->_data['memory'],
						'mounts' => $this->_data['mounts'],
						'swap' => $this->_data['swap']
				);
				
				/* Return only live data */
				return array(
						'name' => self::PLUGIN_OUT_NAME,
						'data' => $liveData	
				);
			}
		}
		
	}
?>