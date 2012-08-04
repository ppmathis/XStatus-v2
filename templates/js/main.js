function updateData() {
	// Get new server data
	$.ajax({
		url: '?return=json',
		cache: false,
		dataType: 'json'
	}).done(function(data) {
		// Call hook if available
		for(plugin in data) {
			pluginName = 'hook_' + plugin.replace('.', '_');
			if(typeof window[pluginName] == 'function') {
				window[pluginName](data[plugin]);
			}
		}
	});
	
	// Update again in ... seconds
	window.setTimeout('updateData()', refreshRate);
}