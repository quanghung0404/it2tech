EasyBlog.module('bookmarklet', function($) {

	var module = this;

	$.bookmarklet = function(elem, type, options, callback) {
		var node = this[type].call($(elem), options);

	    // On IE9, addEventListener() does not necessary fire the onload event
	    // after the script is loaded, therefore we use the attachEvent() method,
	    // as it behaves correctly.
	    if (node.attachEvent && !$.browser.opera) {
	        node.attachEvent("onreadystatechange", callback);
	    } else {
	        node.addEventListener("load", callback, false);
	    }
	};

	$.fn.bookmarklet = function(type, options, callback) {
		var node = this;

		$(document).ready(function(){
			$.bookmarklet[type].call(node, options, callback);
		});
	};

	var linkedinLoaded = false;

	$.bookmarklet.linkedin = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var config = document.createElement('script');
		var script = document.createElement('script');

		var btnOptions = {
			"type": "in/share",
			"data-url": options.url,
			"data-counter": options.size == 'large' ? 'top' : 'right'
		};

		$(config).attr(btnOptions);

		parent.insertBefore(config, node);
		parent.removeChild(node);

		if (!linkedinLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//platform.linkedin.com/in.js';

			linkedinLoaded = true;
		}
	};

	var pocketLoaded = false;

	$.bookmarklet.pocket = function(options) {

		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('a');
		var script = document.createElement('script');
		var btnOptions = {
			"data-pocket-label": "pocket",
			"data-pocket-count": options.size == 'large' ? 'vertical' : 'horizontal',
			"data-save-url": options.url,
			"data-lang": "en"
		};

		$(button)
			.addClass('pocket-btn')
			.attr(btnOptions);

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!pocketLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//widgets.getpocket.com/v1/j/btn.js?v=1';

			pocketLoaded = true;
		}
	};

	var suLoaded = false;

	$.bookmarklet.stumbleupon = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('su:badge');

		var btnOptions = {
			"layout": options.size == 'large' ? 5 : 1,
			"location": options.url
		};

		$(button).attr(btnOptions);

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!suLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//platform.stumbleupon.com/1/widgets.js';

			suLoaded = true;
		}
	};


	window.trackTwitter = function(intent_event) {
		if (intent_event) {
			var opt_pagePath;
			
			if (intent_event.target && intent_event.target.nodeName == 'IFRAME') {
				opt_pagePath = extractParamFromUri(intent_event.target.src, 'url');
			}
			
			_gaq.push(['_trackSocial', 'twitter', 'tweet', opt_pagePath]);					
		}
	}

	$.bookmarklet.twitter = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('a');
		var script = document.createElement('script');
		var layout = options.size == 'large' ? 'vertical' : 'horizontal';
		var btnOptions = {
				"class": "twitter-share-button",
				"href": "https://twitter.com/share",
				"data-url": options.url,
				"data-counturl": options.url,
				"data-count": layout,
				"data-via": options.via,
				"data-text": options.text			
			};

		// Update the button
		$(button).attr(btnOptions).html("Tweet");

		parent.insertBefore(button, node);
		parent.insertBefore(script, node);
		parent.removeChild(node);

		var twttr = window.twttr;

		if (!twttr) {

			$(script)
				.attr({
					type: "text/javascript",
					src: "https://platform.twitter.com/widgets.js"
				})
				.appendTo("head");

			twttr = window.twttr = {
				_e: [],
				ready: function(fn) {
					twttr._e.push(fn)
				}
			};

			if (options.tracking) {
				twttr.ready(function(intent_event){
					twttr.events.bind('tweet', window.trackTwitter);
				});
			}
		}

		return script;
	};

	var hasPlusOne,
		installPlusOne;

	$.bookmarklet.google = function(options) {
		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('g:plusone');
		var btnOptions = {
			"size": options.size == 'large' ? 'tall' : 'medium',
			"href": options.href
		};

		$(button).attr(btnOptions);

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!hasPlusOne) {

			clearTimeout(installPlusOne);

			installPlusOne = setTimeout(function(){

				var head = document.getElementsByTagName("head")[0],
					script = document.createElement("script");

					head.appendChild(script);
					script.type = "text/javascript";
					script.src = "//apis.google.com/js/plusone.js";

				hasPlusOne = true;

			}, 1000);

		} else if (gapi && gapi.plusone) {

			gapi.plusone.go(parent);
		}

		return node;
	};

	var hasFBSDK,
		FBInited,
		parseXFBMLTask,
		parseXFBML = function() {

			// Collect all the FB like calls first
			clearTimeout(parseXFBMLTask);

			parseXFBMLTask = setTimeout(function(){

				// Then finally parse it.
				try {
					FB.XFBML.parse();
				} catch(e) {};

			}, 1000);
		};

	$.bookmarklet.facebook = function(options) {

		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('fb:like');
		var trackFB = function() {
				if (options.tracking) {
			        window.FB.Event.subscribe('edge.create', function(targetUrl) {
			          _gaq.push(['_trackSocial', 'facebook', 'like', targetUrl]);
			        });

			        window.FB.Event.subscribe('edge.remove', function(targetUrl) {
			          _gaq.push(['_trackSocial', 'facebook', 'unlike', targetUrl]);
			        });

			        window.FB.Event.subscribe('message.send', function(targetUrl) {
			          _gaq.push(['_trackSocial', 'facebook', 'send', targetUrl]);
			        });
			    }
			};

		var layout = options.size == 'large' ? 'box_count' : 'button_count';

		$(button)
			.attr({
				"class": "fb-like",
				"data-href": options.url,
				"data-send": options.send,
				"data-layout": layout,
				"data-action": options.verb,
				"data-locale": options.locale,
				"data-colorscheme": options.theme,
				"data-show-faces": false
			});

		parent.insertBefore(button, node);
		parent.removeChild(node);

		// If FBSDK isn't loaded, load it,
		// the social buttons will be parsed by itself.
		if (!window.FB) {

			if (!document.getElementById("fb-root")) {
				$("<div id='fb-root'></div>").prependTo("body");
			}

			var jssdk = document.getElementById("facebook-jssdk");

			// No JSSDK
			if (!jssdk) {

				var head = document.getElementsByTagName("head")[0],
					script = document.createElement("script");

					head.appendChild(script);
					script.id = "facebook-jssdk";
					script.src = "//connect.facebook.net/" + options.locale + "/all.js#xfbml=1";

			// Has JSSDK, but no XFBML support.
			} else if (!FBInited) {

				if (!/xfbml/.test(jssdk.src)) {

					var _fbAsyncInit = window.fbAsyncInit;

					window.fbAsyncInit = function(){

						if ($.isFunction(_fbAsyncInit)) _fbAsyncInit();


						parseXFBML();
						trackFB();
					}
				}

				FBInited = true;
			}

		// If FBSDK is already loaded
		} else {

			parseXFBML();
			trackFB();
		}



		return node;
	};

	var xingLoaded = false;

	$.bookmarklet.xing = function(options) {

		var node = this[0];
		var parent = node.parentNode;
		var button = document.createElement('div');
		var btnOptions = {
			"data-url": options.url,
			"data-counter": options.size == 'small' ? 'right' : 'top',
			"data-type": 'XING/Share'
		};
		
		$(button).attr(btnOptions).html("");

		parent.insertBefore(button, node);
		parent.removeChild(node);

		if (!xingLoaded) {
			var head = document.getElementsByTagName('head')[0];
			var script = document.createElement('script');

			head.appendChild(script);

			script.type = 'text/javascript';
			script.src = '//www.xing-share.com/js/external/share.js';

			xingLoaded = true;
		}
	};

	var vkLoaded = false;

	window.initVk = function(apiKey) {

		if (vkLoaded) {
			return;
		}

		VK.init({
			'apiId': apiKey,
			'onlyWidgets': true 
		});

		vkLoaded = true;
	};

	$.bookmarklet.vk = function(options) {

		var type = options.size == 'large' ? 'vertical' : 'button';

		// Init the script
		window.initVk(options.apiKey);

		VK.Widgets.Like(options.placeholder, {
				"type": type
			});
	};

	module.resolve();
});