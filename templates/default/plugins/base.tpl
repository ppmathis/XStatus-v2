<!-- Template file of Plugin_Base -->
{this scope='plugin.base'}
<script type="text/javascript">
	function hook_plugin_base(data) {
		// Assign simple data
		for(key in data) {
			if(key == 'adv') continue;
			$('#plugin_base_' + key).html(data[key]);
		}
		
		// Assign memory text values
		memory = data['adv']['memory'];
		row = $('#plugin_base_memory_total td');
		row.eq(2).html(memory['free']);
		row.eq(3).html(memory['used']);
		row.eq(4).html(memory['size']);
		row = $('#plugin_base_memory_kernel td');
		row.eq(3).html(memory['kernel']);
		row = $('#plugin_base_memory_buffer td');
		row.eq(3).html(memory['buffer']);
		row = $('#plugin_base_memory_cache td');
		row.eq(3).html(memory['cache']);
		
		// Assign memory meter values
		$('#plugin_base_memory_total td .meter .decal').width(memory['percent_total'] + '%');
		$('#plugin_base_memory_total td .meter p').html(memory['percent_total'] + '%');
		$('#plugin_base_memory_kernel td .meter .decal').width(memory['percent_kernel'] + '%');
		$('#plugin_base_memory_kernel td .meter p').html(memory['percent_kernel'] + '%');
		$('#plugin_base_memory_buffer td .meter .decal').width(memory['percent_buffer'] + '%');
		$('#plugin_base_memory_buffer td .meter p').html(memory['percent_buffer'] + '%');
		$('#plugin_base_memory_cache td .meter .decal').width(memory['percent_cache'] + '%');
		$('#plugin_base_memory_cache td .meter p').html(memory['percent_cache'] + '%');
		
		// Assign swap text values
		swap = data['adv']['swap'];
		row = $('#plugin_base_swap td');
		row.eq(2).html(swap['free']);
		row.eq(3).html(swap['used']);
		row.eq(4).html(swap['total']);
		
		// Assign swap meter values
		$('#plugin_base_base_swap td .meter .decal').width(swap['percent'] + '%');
		$('#plugin_base_base_swap td .meter p').html(swap['percent'] + '%');
		
		// Assign swap drives
		for(key in data['adv']['swap']['drives']) {
			value = data['adv']['swap']['drives'][key];
			
			// Change text values
			row = $('#plugin_base_swaps_' + value['intname'] + ' td');
			row.eq(3).html(value['used']);
			
			// Change meter
			$('#plugin_base_swaps_' + value['intname'] + ' td .meter .decal').width(value['percent'] + '%');
			$('#plugin_base_swaps_' + value['intname'] + ' td .meter p').html(value['percent'] + '%');
		}
		
		// Assign disk values
		for(key in data['adv']['mounts']) {
			value = data['adv']['mounts'][key];
			
			// Change text values
			row = $('#plugin_base_mounts_' + value['intname'] + ' td');
			row.eq(0).html(value['point']);
			row.eq(1).html(value['type']);
			row.eq(2).html(value['partition']);
			row.eq(4).html(value['free']);
			row.eq(5).html(value['used']);
			row.eq(6).html(value['size']);
			
			// Change meter
			$('#plugin_base_mounts_' + value['intname'] + ' td .meter .decal').width(value['percent'] + '%');
			$('#plugin_base_mounts_' + value['intname'] + ' td .meter p').html(value['percent'] + '%');
		}
	}
</script>
<div>
	<!-- [Widget] Server overview -->
	<div class="widget widget_half">
		<div class="widget_title">{this.lang.server_overview}</div>
		<div class="widget_text">
			<table class="data-table">
				<tr class="d0">
					<td>{this.lang.hostname}</td>
					<td>{this.hostname}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.ip}</td>
					<td>{this.ip}</td>
				</tr>
				<tr class="d0">
					<td>{this.lang.kernel}</td>
					<td>{this.kernel}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.distro}</td>
					<td>
						<img src="{gfxpath}/os/{this.distro.icon}" alt="{this.distro.name}" width="16" height="16" style="vertical-align: middle;">
						{this.distro.name}
					</td>
				</tr>
				<tr class="d0">
					<td>{this.lang.uptime}</td>
					<td id="plugin_base_uptime">{this.uptime}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.lastboot}</td>
					<td id="plugin_base_lastboot">{this.lastboot}</td>
				</tr>
				<tr class="d0">
					<td>{this.lang.users}</td>
					<td id="plugin_base_users">{this.users}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.loadavg}</td>
					<td id="plugin_base_loadavg">{this.loadavg}</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- [Widget] Software overview -->
	<div class="widget widget_half">
		<div class="widget_title">{this.lang.software_overview}</div>
		<div class="widget_text">
			<table class="data-table">
				<tr class="d0">
					<td>{this.lang.webserver_version}</td>
					<td>{this.webserver_version}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.php_version}</td>
					<td>{this.php_version}</td>
				</tr>
				<tr class="d0">
					<td>{this.lang.php_sapi}</td>
					<td>{this.php_sapi}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.php_extensions}</td>
					<td>{this.php_extensions}</td>
				</tr>
				<tr class="d0">
					<td>{this.lang.php_gd_version}</td>
					<td>{this.php_gd_version}</td>
				</tr>
				<tr class="d1">
					<td>{this.lang.mysql_version}</td>
					<td>{this.mysql_version}</td>
				</tr>
				<tr class="d0">
					<td>{this.lang.xstatus_version}</td>
					<td>{this.xstatus_version}</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div>
	<!-- Widget memory usage -->
	<div class="widget widget_full">
		<div class="widget_title">{this.lang.memory_usage}</div>
		<div class="widget_text">
			<table class="data-table">
				<tr>
					<th>{this.lang.memory.type}</th>
					<th>{this.lang.memory.usage}</th>
					<th>{this.lang.memory.free}</th>
					<th>{this.lang.memory.used}</th>
					<th>{this.lang.memory.size}</th>
				</tr>
				<tr class="d0" id="plugin_base_memory_total">
					<td style="font-weight: bold;">{this.lang.memory.physical}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {this.memory.percent_total}%;"></span>
							<p>{this.memory.percent_total}%</p>
						</div>
					</td>
					<td>{this.memory.free}</td>
					<td>{this.memory.used}</td>
					<td>{this.memory.total}</td>
				</tr>
				<tr class="d1" id="plugin_base_memory_kernel">
					<td style="padding-left: 25px;">{this.lang.memory.kernel}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {this.memory.percent_kernel}%;"></span>
							<p>{this.memory.percent_kernel}%</p>
						</div>
					</td>
					<td></td>
					<td>{this.memory.kernel}</td>
					<td></td>
				</tr>
				<tr class="d0" id="plugin_base_memory_buffer">
					<td style="padding-left: 25px;">{this.lang.memory.buffer}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {this.memory.percent_buffer}%;"></span>
							<p>{this.memory.percent_buffer}%</p>
						</div>
					</td>
					<td></td>
					<td>{this.memory.buffer}</td>
					<td></td>
				</tr>
				<tr class="d1" id="plugin_base_memory_cache">
					<td style="padding-left: 25px;">{this.lang.memory.cache}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {this.memory.percent_cache}%;"></span>
							<p>{this.memory.percent_cache}%</p>
						</div>
					</td>
					<td></td>
					<td>{this.memory.cache}</td>
					<td></td>
				</tr>
				<tr class="d0" id="plugin_base_swap">
					<td style="font-weight: bold;">{this.lang.memory.swap}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {this.swap.percent}%;"></span>
							<p>{this.swap.percent}%</p>
						</div>
					</td>
					<td>{this.swap.free}</td>
					<td>{this.swap.used}</td>
					<td>{this.swap.total}</td>
				</tr>
				{loop in='this.swap.drives' key='key' out='swapdrive' alt=1}
				<tr class="d{__alt__}" id="plugin_base_swaps_{swapdrive.intname}">
					<td style="padding-left: 25px;">{swapdrive.mount}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {swapdrive.percent}%;"></span>
							<p>{swapdrive.percent}%</p>
						</div>
					</td>
					<td></td>
					<td>{swapdrive.used}</td>
					<td></td>
				</tr>
				{/loop} 
			</table>
		</div>
	</div>
</div>
<!-- [Widget] Mounted filesystems -->
<div>
	<div class="widget widget_full">
		<div class="widget_title">{this.lang.mounts}</div>
		<div class="widget_text">
			<table class="data-table">
				<tr>
					<th>{this.lang.mount.point}</th>
					<th>{this.lang.mount.type}</th>
					<th>{this.lang.mount.partition}</th>
					<th>{this.lang.mount.usage}</th>
					<th>{this.lang.mount.free}</th>
					<th>{this.lang.mount.used}</th>
					<th>{this.lang.mount.size}</th>
				</tr>
				{loop in='this.mounts' key='key' out='mount' alt=0}
				<tr class="d{__alt__}" id="plugin_base_mounts_{mount.intname}">
					<td>{mount.point}</td>
					<td>{mount.type}</td>
					<td>{mount.partition}</td>
					<td style="width: 375px;">
						<div class="meter">
							<span class="decal" style="width: {mount.percent}%;"></span>
							<p>{mount.percent}%</p>
						</div>
					</td>
					<td>{mount.free}</td>
					<td>{mount.used}</td>
					<td>{mount.size}</td>
				</tr>
				{/loop}
			</table>
		</div>
	</div>
</div>