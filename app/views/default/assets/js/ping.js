+function($){
	$(document).on('ready', function(){
		var interval = 1000 * 60 * 5;
		// var interval = 1000 * 60 * 10;
		function haloPing(){
			//get list of user need to check
			var userids = []
			$('[data-halo-userid]').each(function(index, e){
				userids.push($(e).attr('data-halo-userid'))
			});
			// var userids = []
			userids = userids.length?userids.join(','):'';
			$.ajax({
				url: ajaxurl,
				data: {
					'action':'halo_ping',
					'_url': location.href,
					'userids': userids
				},
				success:function(data) {
					setTimeout(haloPing, interval);
					if(data && window.halo !== undefined){
						halo.online.setList(jQuery.parseJSON(data));
					}
				},
				error: function(errorThrown){
				}
			});  		
		}
		haloPing();
	});
}(jQuery);