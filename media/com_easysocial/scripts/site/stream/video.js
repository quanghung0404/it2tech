EasySocial.module("site/stream/video", function($){

	$(document).on("click", "[data-es-links-embed-item]", function() {

        var button = $(this);
        var player = $('<div>').html(button.data('es-stream-embed-player'));
        var embed = '<div class="video-container">' + player.html() + '</div>';

        button.replaceWith(embed);
	});

    // Processes stream items containing videojs embed codes
    $(document).on('click', '[data-es-video-embed]', function() {
        var button = $(this);
        var embed = button.siblings('[data-es-video-embed-player]');

        button.replaceWith(embed);
        embed.removeClass('hide');
    });

	this.resolve();
});
