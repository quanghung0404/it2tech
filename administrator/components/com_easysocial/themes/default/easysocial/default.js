
EasySocial.require()
.done(function($) {

	// Retrieve news
	EasySocial
		.ajax('admin/views/easysocial/getMetaData')
		.done(function(news, localVersion, onlineVersion, outdated) {

			var placeholder = $("[data-widget-news-placeholder]");
			var appNews = $("[data-widget-app-news] > [data-widget-news-items]");
			var versionSection = $('[data-version-status]');
			var currentVersion = $('[data-current-version]');
			var latestVersion = $('[data-latest-version]');
			var installedSection = $('[data-version-installed]');


			currentVersion.html(localVersion);
			latestVersion.html(onlineVersion);

			installedSection.removeClass('hide');

			// Append the app news
			appNews.append(news);

			// Hide placeholder
			placeholder.remove();

			versionSection.removeClass('is-loading');

			// Update version checking
			if (outdated) {
				versionSection.addClass('is-outdated');
			} else {
				versionSection.addClass('is-updated');
			}
		});

	// Bind the news controller on the news widget.
	$('[data-dashboard]').implement(EasySocial.Controller.News);

	$.Joomla('submitbutton', function(task) {

		if (task == 'clearCache') {
			EasySocial.dialog({
				content: EasySocial.ajax( 'admin/views/easysocial/confirmPurgeCache'),
				bindings: {
					"{purgeButton} click" : function() {
						this.form().submit();
						return false;
					} 
				}
			});
		}

	});

});