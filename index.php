<?php
	/*
	 --------------------------------------------------------------------------
	-- XStatus v1.0.0 beta
	-- © 2012 P. Mathis - pmathis@snapserv.net
	--------------------------------------------------------------------------
	-- License info (CC BY-NC-SA 3.0)
	--
	-- This code is licensed via a Creative Commons Licence: http://creativecommons.org/licenses/by-nc-sa/3.0/
	-- Means: 	You may alter the code, but have to give the changes back.
	--			You may not use this work for commercial purposes.
	--			You must attribute the work in the manner specified by the author or licensor.
	-- If you like to use this code commercially, please contact pmathis@snapserv.net
	--------------------------------------------------------------------------
	*/

	/* Config */
	define('XSTATUS_PAGE_TITLE', 'SnapServ.net Status');
	define('XSTATUS_NET_ADAPTER', 'eth0');

	/* Calculate size */
	function file_size($size) {
		$filesizename = array(" KiB", " MiB", " GiB", " TiB", " PiB", " EiB", " ZiB", " YiB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 1) . $filesizename[$i] : '0 KiB';
	}

	/* Get data */
	$data = array();
	$data['Uptime'] = file_get_contents('/proc/uptime');
	$data['Load'] = file_get_contents('/proc/loadavg');
	$data['CPU'] = file_get_contents('/proc/cpuinfo');
	$data['Memory'] = file_get_contents('/proc/meminfo');
	exec('df -P', $dfdata);
	$data['Partitions'] = implode("\n", $dfdata);
	$data['Users'] = exec('users');
	$data['Processes'] = exec('ps aux | wc -l');
	$data['Network'] = file_get_contents('/proc/net/dev');

	/* Generate uptime values */
	$uptime = explode(' ', $data['Uptime']);
	$kernelStart = time() - $uptime[0];
	$values['UpSince'] = date("F d, Y", $kernelStart);
	$values['UpDays'] = round(abs(time()-$kernelStart) / 60 / 60 / 24);
	$values['UpRaw'] = $uptime[0];
	
	/* Generate load values */
	$lod = explode(' ', $data['Load']);
	preg_match('~model name[ \t]*: (.*) @~', $data['CPU'], $matches);
	$cpuname = str_replace('(R)', '&reg;', $matches[1]);
	$cpuname = trim(str_replace('(TM)', '&trade;', $cpuname));
	$values['LoadAvg1'] = $lod[0];
	$values['LoadAvg5'] = $lod[1];
	$values['LoadAvg15'] = $lod[2];
	$values['CPUName'] = $cpuname;
	$values['CPUCores'] = substr_count($data['CPU'], 'processor');
	
	/* Generate memory values */
	preg_match_all('~([A-Za-z_\(\)0-9]*):[\t ]*([0-9]*) kB~', $data['Memory'], $matches);
	$memTotal = array_search('MemTotal', $matches[1]);
	$memTotal = $matches[2][$memTotal];
	$memFree = array_search('MemFree', $matches[1]);
	$memFree = $matches[2][$memFree];
	$memUsed = $memTotal - $memFree;
	$values['MemUsed'] = round($memUsed / 1024, 0);
	$values['MemTotal'] = '/ ' . round($memTotal / 1024, 0) . ' MiB';
	$values['MemPercent'] = round($memUsed / $memTotal * 100) . '% used';
	
	/* Generate swap values */
	$swapTotal = array_search('SwapTotal', $matches[1]);
	$swapTotal = $matches[2][$swapTotal];
	$swapFree = array_search('SwapFree', $matches[1]);
	$swapFree = $matches[2][$swapFree];
	$swapUsed = $swapTotal - $swapFree;
	if($swapTotal != 0) {
		$values['SwapPercent'] = round($swapUsed / $swapTotal * 100) . '% used';
		$values['SwapPercentRaw'] = round($swapUsed / $swapTotal * 100);
	} else {
		$values['SwapPercent'] = '0% used';
		$values['SwapPercentRaw'] = 0;
	}
	$values['SwapUsed'] = round($swapUsed / 1024, 0) . ' MiB';
	$values['SwapTotal'] = round($swapTotal / 1024, 0) . ' MiB';
	
	/* Generate partition data */
	preg_match_all('~^([A-Za-z0-9.\-:#/]*)[\t ]*([0-9]*)[\t ]*([0-9]*)[\t ]*([0-9]*)[\t ]*([0-9]*\%)[\t ]*([A-Za-z0-9.\-:#/]*)[\r\n\t ]*$~m', $data['Partitions'], $matches);
	for($i = 0; $i < sizeof($matches[0]); $i++) {
		$values['Partitions'][] = array(
					'mountPath' => $matches[6][$i],
					'realPath' => $matches[1][$i],
					'spaceUsed' => file_size($matches[3][$i]),
					'spaceTotal' => file_size($matches[4][$i] + $matches[3][$i]),
					'spaceFree' => $matches[4][$i],
					'spacePercent' => $matches[5][$i]
				);
	}
	
	/* Generate sessions data */
	$values['SessionActiveUsers'] = (trim($data['Users']) != '' ? sizeof(explode(' ', $data['Users'])) : '0');
	$values['SessionActiveUsersLabel'] = ($values['SessionActiveUsers'] == 1 ? 'active user' : 'users');
	$values['SessionUserNames'] = str_replace(' ', ', ', $data['Users']);
	$values['SessionActiveProc'] = 'Running processes: ' . $data['Processes'];
	
	/* Generate version values */
	$values['PHPVersionMajor'] = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
	$values['PHPVersion'] = phpversion();
	$values['HTTPVersion'] = $_SERVER['SERVER_SOFTWARE'];
	$values['PHPExtensions'] = count(get_loaded_extensions());
	$values['PHPExtensionsLabel'] = ($values['PHPExtensions'] == 1 ? 'extension' : 'extensions');
	
	/* Generate traffic values */
	preg_match('~^[\t ]*' . XSTATUS_NET_ADAPTER . ':([0-9]*)[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*([0-9]*)[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*[0-9]*[\t ]*~m', $data['Network'], $matches);
	$matches[1] /= 1024;
	$matches[2] /= 1024;
	$values['NetworkReceived'] = file_size($matches[1]);
	$values['NetworkSent'] = file_size($matches[2]);
	$values['NetworkTotal'] = file_size($matches[1] + $matches[2]);
	
	/* Live data required? */
	if(isset($_GET['live'])) {
		switch($_GET['live']) {
			case 'load':
				die(json_encode(array(
					'load-avg1' => $values['LoadAvg1'],
					'load-avg5' => $values['LoadAvg5'],
					'load-avg15' => $values['LoadAvg15']
				)));
				break;
			case 'memory':
				die(json_encode(array(
					'mem-used' => $values['MemUsed'],
					'mem-total' => $values['MemTotal'],
					'mem-percent' => $values['MemPercent']
				)));
				break;
			case 'sessions':
				die(json_encode(array(
						'active-users' => $values['SessionActiveUsers'],
						'active-users-label' => $values['SessionActiveUsersLabel'],
						'active-user-names' => $values['SessionUserNames'],
						'active-processes' => $values['SessionActiveProc']
				)));
			case 'all':
				die(json_encode(array(
						'load-avg1' => $values['LoadAvg1'],
						'load-avg5' => $values['LoadAvg5'],
						'load-avg15' => $values['LoadAvg15'],
						'mem-used' => $values['MemUsed'],
						'mem-total' => $values['MemTotal'],
						'mem-percent' => $values['MemPercent'],
						'active-users' => $values['SessionActiveUsers'],
						'active-users-label' => $values['SessionActiveUsersLabel'],
						'active-user-names' => $values['SessionUserNames'],
						'active-processes' => $values['SessionActiveProc'],
						''
				)));
				break;
			default:
				die('Hacking attempt.');
				break;
		}
	}
?>
<!doctype html>
<html>
	<head>
		<title><?php echo XSTATUS_PAGE_TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="css/reset.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
		<script src="js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>
		<script src="js/uptime.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(function() {
				function liveData(type) {
					$.getJSON('index.php?live=' + type, function(data) {
						$.each(data, function(key, val) {
							$('#' + key).html(val);
						});
					});
				}

				$('.drag-grid').sortable({
					connectWith: '.drag-grid',
					revert: true
				});

				setInterval(function() { liveData('all'); }, 2000);
				doUptime(<?php echo $values['UpRaw']; ?>);
			});
		</script>
	</head>
	<body>
		<div class="container">
			<ul id="drag">
				<!-- Row 1 -->
				<li><ul id="grid-row1" class="grid grid4 drag-grid">
					<!-- Uptime box -->
					<li id="box-uptime" class="grid4-entry grid-box">
						<h3>Uptime</h3>
						<div class="content">
							<div class="main-text">
								<span id="day"><?php echo $values['UpDays']; ?></span>
								<span id="day-label" class="text-label">days</span>
							</div>
							<div class="sub-text">
								<strong id="uptime">~ Loading time ~</strong>
							</div>
						</div>
						<h4><?php echo $values['UpSince']; ?></h4>
					</li>
					<!-- CPU load box -->
					<li id="box-load" class="grid4-entry grid-box">
						<h3>CPU Load</h3>
						<div class="content">
							<div class="main-text">
								<span id="load-avg1"><?php echo $values['LoadAvg1']; ?></span>
								<span id="load-cpucnt" class="text-label">/<?php echo $values['CPUCores']; ?></span>
							</div>
							<div class="sub-text">
								<div style="float: left;"><span id="load-avg5"><?php echo $values['LoadAvg5']; ?></span><br>5 mins</div>
								<div style="float: right;"><span id="load-avg15"><?php echo $values['LoadAvg15']; ?></span><br>15 mins</div>
							</div>
						</div>
						<h4><?php echo $values['CPUName']; ?></h4>
					</li>
					<!-- Memory usage box -->
					<li id="box-memory" class="grid4-entry grid-box">
						<h3>Memory usage</h3>
						<div class="content">
							<div id="mem-used" class="main-text"><?php echo $values['MemUsed']; ?></div>
							<div><span id="mem-total" style="padding-left: 20px" class="text-label"><?php echo $values['MemTotal']; ?></span></div>
						</div>
						<h4 id="mem-percent"><?php echo $values['MemPercent']; ?></h4>
					</li>
					<!-- Swap usage box -->
					<li id="box-swap" class="grid4-entry grid-box">
						<h3>Swap usage</h3>
						<div class="content">
							<div class="center_img">
								<img src="https://chart.googleapis.com/chart?chl=<?php echo $values['SwapPercentRaw']; ?>%&chs=200x100&cht=gm&chco=77AB10,FFFF00|FF0000&chd=t:<?php echo $values['SwapPercentRaw']; ?>&chf=bg,s,232526" alt="Swap" />
							</div>
						</div>
						<h4><?php echo $values['SwapUsed']; ?> used of <?php echo $values['SwapTotal']; ?></h4>
					</li>
				</ul></li>
				
				<!-- Partitions -->
				<li><p class="clear">&nbsp;</p><ul class="grid grid1 clear">
					<li id="box-partitions" class="grid1-entry grid-box">
						<h3>Partitions</h3>
						<table>
							<tbody>
								<?php foreach($values['Partitions'] as $partition) { ?>
								<tr>
									<td width="25%" style="padding-left: 20px;"><?php echo $partition['mountPath']; ?></td>
									<td width="50%">
										<div class="meter">
											<span class="decal" style="width: <?php echo ((449 / 100) * $partition['spacePercent']); ?>px;"></span>
										</div>
									</td>
									<td width="25%">
										<span class="partition-percent"><?php echo $partition['spacePercent']; ?></span>
										<strong><?php echo $partition['spaceUsed']; ?> / <?php echo $partition['spaceTotal']; ?></strong>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</li>
				</ul></li>
				
				<!-- Row 2 -->
				<li><ul id="grid-row2" class="grid grid4 drag-grid">
					<!-- Process box -->
					<li id="box-process" class="grid4-entry grid-box">
						<h3>Active sessions</h3>
						<div class="content">
							<div class="main-text">
								<span id="active-users"><?php echo $values['SessionActiveUsers']; ?></span>
								<span id="active-users-label" class="text-label"><?php echo $values['SessionActiveUsersLabel']; ?></span>
							</div>
							<div class="sub-text">
								<strong id="active-user-names"><?php echo $values['SessionUserNames']; ?></strong>
							</div>
						</div>
						<h4 id="active-processes"><?php echo $values['SessionActiveProc']; ?></h4>
					</li>
					<!-- PHP Version box -->
					<li id="box-php-version" class="grid4-entry grid-box">
						<h3>Software versions</h3>
						<div class="content">
							<div class="main-text">
								<span id="php-version">PHP</span>
								<span id="php-version-label" class="text-label"><?php echo $values['PHPVersionMajor']; ?></span>
							</div>
							<div class="sub-text">
								<strong>Running <?php echo $values['PHPVersion']; ?><br />on <?php echo $values['HTTPVersion']; ?></strong>
							</div>
						</div>
						<h4 id="php-extensions"><?php echo $values['PHPExtensions']; ?> <?php echo $values['PHPExtensionsLabel']; ?> loaded</h4>
					</li>
					<!-- Network box -->
					<li id="box-php-network" class="grid4-entry grid-box">
						<h3>Network traffic</h3>
						<div class="content">
							<div class="main-text">
								<span id="net-label">Net</span>
								<span id="net-traffic" class="text-label"><?php echo $values['NetworkTotal']; ?></span>
							</div>
							<div class="sub-text">
								<strong>Data sent: <?php echo $values['NetworkSent']; ?><br />Data received: <?php echo $values['NetworkReceived']; ?></strong>
							</div>
						</div>
						<h4 id="net-adapter">Network adapter: <?php echo XSTATUS_NET_ADAPTER; ?></h4>
					</li>
					<!-- Credits box -->
					<li id="box-credits" class="grid4-entry grid-box">
						<h3>Credits</h3>
						<div class="content">
							<div class="center_img">
								<a href="https://www.snapserv.net/" target="blank"><img src="logo.png" style="margin-top: 15px; width: 213px; height: 123px;" alt="XStatus Lite" /></a>
							</div>
						</div>
						<h4>&copy; 2011 - <?php echo date("Y"); ?> P. Mathis</h4>
					</li>
				</ul></li>
			</ul>
		</div>
	</body>
</html>