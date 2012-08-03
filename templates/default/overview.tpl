{include file='page_header.tpl'}
<div class="container">
	<!-- [Widget] Server overview -->
	<div>
		<div class="widget widget_half">
			<div class="widget_title">{lang.server_overview}</div>
			<div class="widget_text">
				<table class="data-table">
					<tr class="d0">
						<td>{lang.hostname}</td>
						<td>{data.hostname}</td>
					</tr>
					<tr class="d1">
						<td>{lang.ip}</td>
						<td>{data.ip}</td>
					</tr>
					<tr class="d0">
						<td>{lang.kernel}</td>
						<td>{data.kernel}</td>
					</tr>
					<tr class="d1">
						<td>{lang.distro}</td>
						<td>
							<img src="{gfxpath}/os/{data.distro.icon}" alt="{data.distro.name}" width="16" height="16" style="vertical-align: middle;">
							{data.distro.name}
						</td>
					</tr>
					<tr class="d0">
						<td>{lang.uptime}</td>
						<td>{data.uptime}</td>
					</tr>
					<tr class="d1">
						<td>{lang.lastboot}</td>
						<td>{data.lastboot}</td>
					</tr>
					<tr class="d0">
						<td>{lang.users}</td>
						<td>{data.users}</td>
					</tr>
					<tr class="d1">
						<td>{lang.loadavg}</td>
						<td>{data.loadavg}</td>
					</tr>
				</table>
			</div>
		</div>
		<!-- [Widget] Software overview -->
		<div class="widget widget_half">
			<div class="widget_title">{lang.software_overview}</div>
			<div class="widget_text">
				<table class="data-table">
					<tr class="d0">
						<td>{lang.webserver_version}</td>
						<td>{data.webserver_version}</td>
					</tr>
					<tr class="d1">
						<td>{lang.php_version}</td>
						<td>{data.php_version}</td>
					</tr>
					<tr class="d0">
						<td>{lang.php_sapi}</td>
						<td>{data.php_sapi}</td>
					</tr>
					<tr class="d1">
						<td>{lang.php_extensions}</td>
						<td>{data.php_extensions}</td>
					</tr>
					<tr class="d0">
						<td>{lang.php_gd_version}</td>
						<td>{data.php_gd_version}</td>
					</tr>
					<tr class="d1">
						<td>{lang.mysql_version}</td>
						<td>{data.mysql_version}</td>
					</tr>
					<tr class="d0">
						<td>{lang.xstatus_version}</td>
						<td>{data.xstatus_version}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<!-- Memory usage -->
	<div>
		<div class="widget widget_full">
			<div class="widget_title">{lang.memory_usage}</div>
			<div class="widget_text">
				<table class="data-table">
					<tr>
						<th>{lang.memory.type}</th>
						<th>{lang.memory.usage}</th>
						<th>{lang.memory.free}</th>
						<th>{lang.memory.used}</th>
						<th>{lang.memory.size}</th>
					</tr>
					<tr  class="d0">
						<td style="font-weight: bold;">{lang.memory.physical}</td>
						<td style="width: 375px;">
							<div class="meter">
								<span class="decal" style="width: {data.memory.percent_total}%;"></span>
								<p>{data.memory.percent_total}%</p>
							</div>
						</td>
						<td>{data.memory.free}</td>
						<td>{data.memory.used}</td>
						<td>{data.memory.total}</td>
					</tr>
					<tr  class="d1">
						<td style="padding-left: 25px;">{lang.memory.kernel}</td>
						<td style="width: 375px;">
							<div class="meter">
								<span class="decal" style="width: {data.memory.percent_kernel}%;"></span>
								<p>{data.memory.percent_kernel}%</p>
							</div>
						</td>
						<td></td>
						<td>{data.memory.kernel}</td>
						<td></td>
					</tr>
					<tr class="d0">
						<td style="padding-left: 25px;">{lang.memory.buffer}</td>
						<td style="width: 375px;">
							<div class="meter">
								<span class="decal" style="width: {data.memory.percent_buffer}%;"></span>
								<p>{data.memory.percent_buffer}%</p>
							</div>
						</td>
						<td></td>
						<td>{data.memory.buffer}</td>
						<td></td>
					</tr>
					<tr class="d1">
						<td style="padding-left: 25px;">{lang.memory.cache}</td>
						<td style="width: 375px;">
							<div class="meter">
								<span class="decal" style="width: {data.memory.percent_cache}%;"></span>
								<p>{data.memory.percent_cache}%</p>
							</div>
						</td>
						<td></td>
						<td>{data.memory.cache}</td>
						<td></td>
					</tr>
					<tr class="d0">
						<td style="font-weight: bold;">{lang.memory.swap}</td>
						<td style="width: 375px;">
							<div class="meter">
								<span class="decal" style="width: {data.swap.percent}%;"></span>
								<p>{data.swap.percent}%</p>
							</div>
						</td>
						<td>{data.swap.free}</td>
						<td>{data.swap.used}</td>
						<td>{data.swap.total}</td>
					</tr>
					{loop in='data.swap.drives' key='key' out='swapdrive' alt=1}
					<tr class="d{__alt__}">
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
	<!-- Mounted filesystems -->
	<div>
		<div class="widget widget_full">
			<div class="widget_title">{lang.mounts}</div>
			<div class="widget_text">
				<table class="data-table">
					<tr>
						<th>{lang.mount.point}</th>
						<th>{lang.mount.type}</th>
						<th>{lang.mount.partition}</th>
						<th>{lang.mount.usage}</th>
						<th>{lang.mount.free}</th>
						<th>{lang.mount.used}</th>
						<th>{lang.mount.size}</th>
					</tr>
					{loop in='data.mounts' key='key' out='mount' alt=0}
					<tr class="d{__alt__}">
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
</div>
{include file='page_footer.tpl'}