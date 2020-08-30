jQuery('a.mlvod-line-link').click(function(e){
	e.preventDefault();
	var line = jQuery(this).attr('line');
	var vp = jQuery(this).attr('vp');
	var player = videojs('#' + vp);
	var player_time = player.currentTime();
	jQuery('a.mlvod-line-link').removeClass('active');
	jQuery(this).addClass('active');

	player.ready(function() {
		var rd = Math.random();
		this.src({
			src: line,
			type: 'application/x-mpegURL',
		});
		this.on("loadedmetadata", function(){
			this.currentTime(player_time);
		});
	});
});