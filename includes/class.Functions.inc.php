<?php
	/**
	 * Useful functions for other classes
	 *
	 * @category 	PHP
	 * @package 	XStatus
	 * @author 		Pascal Mathis <pmathis@snapserv.net>
	 * @copyright 	2012 XStatus
	 * @license 	http://www.snapserv.net/xstatus_license Custom license
	 * @link 		http://www.snapserv.net/xstatus
	 */

	class Functions {
		
		/**
		 * Read a file and return it as a string
		 * @param string 	$filename 	Path to the file
		 * @param string 	$result 	Variable where file contents should be stored
		 * @param integer	$lines		Number of lines to read
		 * @param integer	$bytes		Number of bytes to read per line
		 * @param boolean 	$errorRep	Enable/disable error reporting
		 * @return void
		 */
		public static function readFile($filename, &$result, $lines = 0, $bytes = 4096, $errorRep = true) {
			$tmpResult = '';
			$currentLine = 1;
			$error = Error::getInstance();
			
			/* Try to open file */
			if(file_exists($filename)) {
				if($handle = fopen($filename, 'r')) {
					/* Fetch line by line */
					while(!feof($handle)) {
						$tmpResult .= fgets($handle, $bytes);
						
						/* Only the desired amount of lines (0 = all) */
						if($lines <= $currentLine && $lines != 0) {
							break;
						} else {
							$currentLine++;
						}
					}
					
					/* Close handle and return */
					fclose($handle);
					$result = $tmpResult;
					return true;
				} else {
					if($errorRep) {
						$error->addError('fopen(' . $filename . ')', 'Can not read file.');
					}
					return false;
				}
			} else {
				if($errorRep) {
					$error->addError('file_exists(' . $filename . ')', 'The file does not exist.');
				}
			}
		}
		
		/**
		 * Find a system program
		 * @param string	$program	Name of the program
		 * @return string Complete path and name of the program
		 */
		private static function _findProgram($program) {
			/* Get path */
			$arrPath = array();
			if(PHP_OS == 'WINNT') {
				$program .= '.exe';
				$arrPath = preg_split('~;~', getenv("Path"), -1, PREG_SPLIT_NO_EMPTY);
			} else {
				$arrPath = preg_split('~;~', getenv("PATH"), -1, PREG_SPLIT_NO_EMPTY);
			}
			
			/* Add some default paths */
			if(empty($arrPath) && PHP_OS != 'WINNT') {
				array_push($arrPath, '/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
			}
			
			/* Avoid open_basedir errors */
			if((bool) ini_get('open_basedir')) {
				$open_basedir = preg_split('~:~', ini_get('open_basedir'), -1, PREG_SPLIT_NO_EMPTY);
			}
			
			/* Scan paths */
			foreach($arrPath as $path) {
				/* Avoid open_basedir errors */
				if((isset($open_basedir) && !in_array($path, $open_basedir)) || !is_dir($path)) {
					continue;
				}
				
				/* Check if an executable file exists */
				$filePath = $path . '/' . $program;
				if(is_executable($filePath)) {
					return $filePath;
				}
			}
		}
		
		/**
		 * Execute a system program, return a trimmed result
		 * Strict pipe checking (' | ', not '|')
		 * @param string	$program	Name of the program
		 * @param string	$args		Arguments to the program
		 * @param string	&$buffer	Output of the command
		 * @param boolean 	$errorRep	Enable/disable error reporting
		 * @return boolean True if success
		 */
		public static function executeProgram($program, $args, &$buffer, $errorRep = true) {
			$buffer = '';
			$errorStr = '';
			$pipes = array();
			
			/* Get program path */
			$programPath = self::_findProgram($program);
			$error = Error::getInstance();
			if(!$programPath) {
				if($errorRep) {
					$error->addError('findProgram(' . $program . ')', 'Program not found');
				}
				return false;
			}
			
			/* Check arguments */
			if($args) {
				$argArray = preg_split('~ ~', $args, -1, PREG_SPLIT_NO_EMPTY);
				for($i = 0, $argCount = count($argArray); $i < $argCount; $i++) {
					if($argArray[$i] == '|') {
						$cmdStr = $argArray[$i + 1];
						$newCmdStr = self::_findProgram($cmdStr);
						$args = preg_replace('~\| ' . $cmdStr . '~', '| ' . $newCmdStr, $args);
					}
				}
			}
			
			/* Execute program */
			$descriptor = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
			$process = proc_open($programPath . ' ' . $args, $descriptor, $pipes);
			if(is_resource($process)) {
				$buffer .= self::_readstream($pipes, $buffer, $errorStr);
				$returnval = proc_close($process);
			}
			
			/* Get return value */
			$errorStr = trim($errorStr);
			$buffer = trim($buffer);
			
			if(!empty($errorStr) && $returnval <> 0) {
				if($errorRep) {
					$error->addError($programPath, $errorStr . "\nReturn value: " . $returnval);
				}
				return false;
			}
			if(!empty($errorStr)) {
				if($errorRep) {
					$error->addError($programPath, $errorStr . "\nReturn value: " . $returnval);
				}
				return true;
			}
			
			return true;
		}
		
		/**
		 * Read a file stream
		 * @param array			$pipes		Pipes to use
		 * @param string		&$out 		Buffer to store stdout
		 * @param string		&$err 		Buffer to store stderr
		 * @param int 			$timeout	Timeout
		 * @return void
		 */
		private static function _readstream($pipes, &$out, &$err, $timeout = 10) {
			/* Fill output string */
			$w = null;
			$e = null;
			
			/* Read stdout */
			$time = $timeout;
			while($time >= 0) {
				$read = array($pipes[1]);
				while(!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0) {
					$out .= fread($read[0], 4096);
				}
				$time--;
			}
			
			/* Read stderr */
			$time = $timeout;
			while($time >= 0) {
				$read = array($pipes[2]);
				while(!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0) {
					$err .= fread($read[0], 4096);
				}
				$time--;
			}
		}
		
		/**
		 * Convert bytes into a human readable format
		 * @param	int		$bytes		Bytes to convert
		 * @param	int		$precision	Precision
		 */
		public static function convertBytes($bytes, $precision) {
			$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB');
			if($bytes === 0) return '0 ' . $units[0];
			return number_format(@round(
				$bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision
			), $precision) . ' ' . $units[$i];
		}
		
		/**
		 * Parse the output of df
		 * @param	string	$params		Parameters
		 * @return	array	Disk devices
		 */
		public static function parseDf($params) {
			$result = array();
			$df = '';
			$mount = '';
			$mountData = array();
			
			if(Functions::executeProgram('df', '-k ' . $params, $df, XSTATUS_DEBUG)) {
				$df_lines = preg_split("~\n~", $df, -1, PREG_SPLIT_NO_EMPTY);
				if(Functions::executeProgram('mount', '', $mount, XSTATUS_DEBUG)) {
					/* Process data from 'mount' */
					$mount_lines = preg_split("~\n~", $mount, -1, PREG_SPLIT_NO_EMPTY);
					foreach($mount_lines as $mount_line) {
						if(preg_match('~\S+ on (\S+) type (.*) \((.*)\)~', $mount_line, $data)) {
							$mountData[$data[1]]['fstype'] = $data[2];
							if(XSTATUS_SHOW_MOUNT_OPTION) $mountData[$data[1]]['options'] = $data[3];
						} else if(preg_match('~\S+ (.*) on (\S+) \((.*)\)~', $mount_line, $data)) {
							$mountData[$data[2]]['fstype'] = $data[1];
							if(XSTATUS_SHOW_MOUNT_OPTION) $mountData[$data[2]]['options'] = $data[3];
						} else if(preg_match('~\S+ on ([\S ]+) \((\S+)(,\s(.*))?\)~', $mount_line, $data)) {
							$mountData[$data[1]]['fstype'] = $data[2];
							if(XSTATUS_SHOW_MOUNT_OPTION) $mountData[$data[1]]['options'] = isset($data[4]) ? $data[4] : '';
						}
					}
					
					/* Process data from 'df' */
					foreach($df_lines as $df_line) {
						$data1 = preg_split('~(\%\s)~', $df_line, 2);
						if(count($data1) != 2) continue;
						
						if(preg_match('~(.*)(\s+)(([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)$)~', $data1[0], $data2)) {
							$data = array($data2[1], $data2[4], $data2[6], $data2[8], $data2[10], $data1[1]);
							if(count($data) == 6) {
								$data[5] = trim($data[5]);
								$device = array();
								$device['name'] = trim($data[0]);
								if($data[2] < 0) {
									$device['total'] = $data[3] * 1024;
									$device['used'] = $data[3] * 1024;
								} else {
									$device['total']  = $data[1] * 1024;
									$device['used'] = $data[2] * 1024;
									$device['free'] = $data[3] * 1024;
								}
								
								if(isset($mountData[$data[5]])) {
									$device['type'] = $mountData[$data[5]]['fstype'];
									if(XSTATUS_SHOW_MOUNT_OPTION) {
										if(XSTATUS_SHOW_MOUNT_CREDENTIALS) {
											$device['options'] = $mountData[$data[5]]['options'];
										} else {
											$tmp = $mountData[$data[5]]['options'];
											$tmp = preg_replace('~(^guest,)|(^guest$)|(,guest$)~i', '', $tmp);
											$tmp = preg_replace('~,guest,~i', '', $tmp);
											$tmp = preg_replace('~(^user=[^,]*,)|(^user=[^,]*$)|(,user=[^,]*$)~i', '', $tmp);
											$tmp = preg_replace('~,user=[^,]*,~i', '', $tmp);
											$tmp = preg_replace('~(^password=[^,]*,)|(^password=[^,]*$)|(,password=[^,]*$)~i', '', $tmp);
											$tmp = preg_replace('~,password=[^,]*,~i', '', $tmp);
											$device['options'] = $tmp;
											unset($tmp);
										}
									}
								}
								
								$result[] = $device;
							}
						}
					}
				}
			}
			return $result;
		}
		
	}
?>