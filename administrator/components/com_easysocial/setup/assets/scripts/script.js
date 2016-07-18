
var es =
{
	ajaxUrl: "<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&ajax=1",
	installation: {
		path: null,
		ajaxCall: function(task, properties, callback) {

			var prop = $.extend({
									"apikey": "<?php echo JRequest::getVar('apikey' , '');?>",
									"path": es.installation.path
								}, properties);

			$.ajax({
				type: "POST",
				url: es.ajaxUrl + "&controller=installation&task=" + task ,
				data: prop
			}).done(function(result) {
				callback.apply(this, [result]);
			});
		},

		showRetry: function(step) {
			$('[data-installation-retry]').data('retry-step', step).show();
			$('[data-installation-loading]').hide();
		},

		extract: function(packageName) {
			es.installation.ajaxCall('extract', {
				"package": packageName
			}, function(result) {

				es.installation.update('data-progress-extract', result, '10%');

				if (!result.state) {
					return false;
				}

				es.installation.path = result.path;

				es.installation.runSQL();
			});
		},

		download: function() {
			es.installation.ajaxCall('download', {}, function(result){

				// Set the progress
				es.installation.update( 'data-progress-download' , result , '10%');

				if (!result.state) {
					es.installation.showRetry('download');
					return false;
				}

				// Set the installation path
				es.installation.path = result.path;
				es.installation.runSQL();
			});
		},

		runSQL 	: function()
		{
			// Install the SQL stuffs
			es.installation.setActive( 'data-progress-sql' );

			es.installation.ajaxCall( 'installSQL' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-sql' , result , '15%');

				if( !result.state )
				{
					es.installation.showRetry( 'runSQL' );
					return false;
				}

				es.installation.installAdmin();
			});
		},

		installAdmin : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-admin' );

			es.installation.ajaxCall( 'installCopy' , { "type" : "admin" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-admin' , result , '20%');

				if( !result.state )
				{
					es.installation.showRetry( 'installAdmin' );
					return false;
				}

				es.installation.installSite();
			});
		},

		installSite : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-site' );

			es.installation.ajaxCall( 'installCopy' , { "type" : "site" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-site' , result , '25%');

				if( !result.state )
				{
					es.installation.showRetry( 'installAdmin' );
					return false;
				}

				es.installation.installLanguages();
			});
		},

		installLanguages : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-languages' );

			es.installation.ajaxCall( 'installCopy' , { "type" : "languages" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-languages' , result , '25%');

				if( !result.state )
				{
					es.installation.showRetry('installLanguages');
					return false;
				}

				es.installation.installMedia();
			});
		},

		installMedia : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-media' );

			es.installation.ajaxCall( 'installCopy' , { "type" : "media" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-media' , result , '30%');

				if( !result.state )
				{
					es.installation.showRetry( 'installMedia' );
					return false;
				}

				es.installation.installFoundry();
			});
		},

		installFoundry: function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-foundry' );

			es.installation.ajaxCall( 'installCopy' , { "type" : "foundry" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-foundry' , result , '35%');

				if( !result.state )
				{
					es.installation.showRetry( 'installFoundry' );
					return false;
				}

				es.installation.syncDB();
			});
		},

		syncDB: function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-syncdb' );

			es.installation.ajaxCall( 'syncDB' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-syncdb' , result , '38%');

				if( !result.state )
				{
					es.installation.showRetry( 'syncDB' );
					return false;
				}

				es.installation.installUserApps();
			});
		},

		installUserApps: function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-userapps' );

			es.installation.ajaxCall( 'installApps' , { "group" : "user" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-userapps' , result , '40%');

				if( !result.state )
				{
					es.installation.showRetry( 'installUserApps' );
					return false;
				}

				es.installation.installGroupApps();
			});
		},

		installGroupApps: function()
		{
			es.installation.setActive( 'data-progress-groupapps' );

			es.installation.ajaxCall( 'installApps' , { "group" : "group" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-groupapps' , result , '45%');

				if( !result.state )
				{
					es.installation.showRetry( 'installGroupApps' );
					return false;
				}

				es.installation.installEventApps();
			});
		},

		installEventApps: function()
		{
			es.installation.setActive('data-progress-eventapps');

			es.installation.ajaxCall('installApps' , { "group" : "event" } , function(result) {
				// Set the progress
				es.installation.update('data-progress-eventapps', result, '48%');

				if( !result.state )
				{
					es.installation.showRetry('installEventApps');
					return false;
				}

				es.installation.installUserFields();
			});
		},

		installUserFields: function()
		{
			es.installation.setActive( 'data-progress-userfields' );

			es.installation.ajaxCall( 'installFields' , { "group" : "user" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-userfields' , result , '50%');

				if( !result.state )
				{
					es.installation.showRetry( 'installUserFields' );
					return false;
				}

				es.installation.installGroupFields();
			});
		},

		installGroupFields: function()
		{
			es.installation.setActive( 'data-progress-groupfields' );

			es.installation.ajaxCall( 'installFields' , { "group" : "group" } , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-groupfields' , result , '55%');

				if( !result.state )
				{
					es.installation.showRetry( 'installGroupFields' );
					return false;
				}

				es.installation.installEventFields();
			});
		},

		installEventFields: function()
		{
			es.installation.setActive('data-progress-eventfields');

			es.installation.ajaxCall('installFields', { "group" : "event" } , function(result) {
				// Set the progress
				es.installation.update( 'data-progress-eventfields' , result , '58%');

				if (!result.state) {
					es.installation.showRetry('installEventFields');
					return false;
				}

				es.installation.installPlugins();
			});
		},

		installPlugins: function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-plugins' );

			es.installation.ajaxCall( 'installPlugins' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-plugins' , result , '60%');

				if( !result.state )
				{
					es.installation.showRetry( 'installPlugins' );
					return false;
				}

				es.installation.installModules();
			});
		},

		installModules: function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-modules' );

			es.installation.ajaxCall( 'installModules' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-modules' , result , '65%');

				if( !result.state )
				{
					es.installation.showRetry( 'installModules' );
					return false;
				}

				es.installation.installBadges();
			});
		},

		installBadges : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-badges' );

			es.installation.ajaxCall( 'installBadges' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-badges' , result , '70%');

				if( !result.state )
				{
					es.installation.showRetry( 'installBadges' );
					return false;
				}

				es.installation.installPoints();
			});
		},

		installPoints : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-points' );

			es.installation.ajaxCall( 'installPoints' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-points' , result , '75%');

				if( !result.state )
				{
					es.installation.showRetry( 'installPoints' );
					return false;
				}

				es.installation.installAccess();
			});
		},

		installAccess : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-access' );

			es.installation.ajaxCall( 'installAccess' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-access' , result , '78%');

				if( !result.state )
				{
					es.installation.showRetry( 'installAccess' );
					return false;
				}

				es.installation.installPrivacy();
			});
		},

		installPrivacy : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-privacy' );

			es.installation.ajaxCall( 'installPrivacy' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-privacy' , result , '80%');

				if( !result.state )
				{
					es.installation.showRetry( 'installPrivacy' );
					return false;
				}

				es.installation.installProfiles();
			});
		},

		installProfiles : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-profiles' );

			es.installation.ajaxCall( 'installProfiles' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-profiles' , result , '85%');

				if( !result.state )
				{
					es.installation.showRetry( 'installProfiles' );
					return false;
				}

				es.installation.installGroupCategories();
			});
		},

		installGroupCategories : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-groupcategories' );

			es.installation.ajaxCall( 'installDefaultGroupCategories' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-groupcategories' , result , '90%');

				if( !result.state )
				{
					es.installation.showRetry( 'installDefaultGroupCategories' );
					return false;
				}

				es.installation.installEventCategories();
			});
		},

		installEventCategories : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-eventcategories' );

			es.installation.ajaxCall( 'installDefaultEventCategories' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-eventcategories' , result , '92%');

				if( !result.state )
				{
					es.installation.showRetry( 'installDefaultEventCategories' );
					return false;
				}

				es.installation.installVideoCategories();
			});
		},

		installVideoCategories : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-videocategories' );

			es.installation.ajaxCall( 'installDefaultVideoCategories' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-videocategories' , result , '94%');

				if( !result.state )
				{
					es.installation.showRetry( 'installDefaultVideoCategories' );
					return false;
				}

				es.installation.installAlerts();
			});
		},

		installAlerts : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-alerts' );

			es.installation.ajaxCall( 'installAlerts' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-alerts' , result , '95%');

				if( !result.state )
				{
					es.installation.showRetry( 'installAlerts' );
					return false;
				}

				es.installation.postInstall();
			});
		},

		postInstall : function()
		{
			// Install the admin stuffs
			es.installation.setActive( 'data-progress-postinstall' );

			es.installation.ajaxCall( 'installPost' , {} , function( result )
			{
				// Set the progress
				es.installation.update( 'data-progress-postinstall' , result , '100%');

				if( !result.state )
				{
					es.installation.showRetry( 'postInstall' );
					return false;
				}

				$( '[data-installation-completed]' ).show();

				$( '[data-installation-loading]' ).hide();
				$( '[data-installation-submit]' ).show();

				$( '[data-installation-submit]' ).bind( 'click' , function(){
					$( '[data-installation-form]' ).submit();
				});

			});
		},

		update : function( element , obj , progress )
		{
			var className 		= obj.state ? ' text-success' : ' text-error',
				stateMessage	= obj.state ? 'Success' : 'Failed';

			// Update the state
			$( '[' + element + ']' )
			.find( '.progress-state' )
			.html( stateMessage )
			.removeClass( 'text-info' )
			.addClass( className );

			// Update the message
			$( '[' + element + ']' )
			.find( '.notes' )
			.html( obj.message )
			.removeClass( 'text-info' )
			.addClass( className );

			// Update the progress
			es.installation.updateProgress( progress );
		},

		updateProgress	: function( percentage )
		{
			$( '[data-progress-bar]' ).css( 'width' , percentage );
			$( '[data-progress-bar-result]' ).html( percentage );
		},

		setActive 	: function( item )
		{
			$( '[data-progress-active-message]' ).html( $( '[' + item + ']' ).find( '.split__title' ).html() + ' ...' );
			$( '[' + item + ']' ).removeClass( 'pending' ).addClass( 'active' );
		}
	},
	maintenance :
	{
		init: function()
		{
			// Initializes the installation process.
			es.maintenance.syncUsers();
		},
		syncUsers : function()
		{
			$.ajax(
			{
				type 	: "POST",
				url 	: es.ajaxUrl + "&controller=maintenance&task=syncUsers"
			})
			.done(function( result )
			{
				$( '[data-progress-syncuser]' ).addClass( 'active' ).removeClass( 'pending' );

				var item		= $( '<li>' ),
					className	= result.state ? 'text-success' : 'text-error';

				$( item )
				.addClass( className )
				.html( result.message );

				$( '[data-progress-syncuser-items]' ).append( item );

				// If there are more items to process, call itself again.
				if( result.state == 2 )
				{
					return es.maintenance.syncUsers();
				}

				// If there are nothing more to do here, switch out
				$( '[data-progress-syncuser]' )
					.find( '.progress-state' )
					.html( result.stateMessage )
					.addClass( 'text-success' )
					.removeClass( 'text-info' );

				es.maintenance.syncProfiles();
			});
		},
		syncProfiles : function()
		{
			$( '[data-progress-syncprofiles]' ).addClass( 'active' ).removeClass( 'pending' );

			$.ajax(
			{
				type 	: "POST",
				url 	: es.ajaxUrl + "&controller=maintenance&task=syncProfiles"
			})
			.done( function( result )
			{
				var item 	= $( '<li>' ),
					className	= result.state ? 'text-success' : 'text-error';

				$( item )
				.addClass( className )
				.html( result.message );

				$( '[data-progress-syncprofile-items]' ).append( item );

				// If there are more items to process, call itself again.
				if( result.state == 2 )
				{
					return es.maintenance.syncProfiles();
				}

				$( '[data-progress-syncprofiles]' )
					.find( '.progress-state' )
					.html( result.stateMessage )
					.addClass( 'text-success' )
					.removeClass( 'text-info' );

				es.maintenance.execMaintenance();
			});
		},

		execMaintenance: function()
		{
			var frame = $('[data-progress-execscript]');

			frame.addClass('active').removeClass('pending');

			$.ajax({
				type: 'POST',
				url: es.ajaxUrl + '&controller=maintenance&task=getScripts'
			})
			.done(function(result){

				var item = $('<li>');
				item.addClass('text-success').html(result.message);

				$('[data-progress-execscript-items]').append(item);

				es.maintenance.runScript(result.scripts, 0);
			});
		},

		runScript: function(scripts, index)
		{
			if(scripts[index] === undefined)
			{
				// If the logics come here, means we are done with running scripts

				// run script completed. lets update the scriptversion
				$.ajax({
					type: 'POST',
					url: es.ajaxUrl + '&controller=maintenance&task=updateScriptVersion'
				}).done(function(result) {
					var item = $('<li>');
					item.addClass('text-success').html(result.message);
					$('[data-progress-execscript-items]').append(item);

					$( '[data-progress-execscript]' )
						.find( '.progress-state' )
						.html( result.stateMessage )
						.addClass( 'text-success' )
						.removeClass( 'text-info' );

					es.maintenance.complete();
				});

				return true;
			}

			$.ajax({
				type: 'POST',
				url: es.ajaxUrl + '&controller=maintenance&task=runScript',
				data: {
					script: scripts[index]
				}
			})
			.always(function(result) {

				var item = $('<li>'),
					className	= result.state ? 'text-success' : 'text-error';

				item.addClass(className).html(result.message);

				$('[data-progress-execscript-items]').append(item);

				es.maintenance.runScript(scripts, ++index);
			});
		},

		complete: function()
		{
			$('[data-installation-loading]').hide();
			$('[data-installation-submit]').show();

			$('[data-installation-submit]').on('click', function() {
				$('[data-installation-form]').submit();
			});
		}
	}
}
