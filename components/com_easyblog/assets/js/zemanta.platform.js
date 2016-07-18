(function() {
	var $ = null;

	function setPlatform( $ , p)
	{

		return $.zextend(p, {
			widget_version: 3,
			platform: {
				dnd_supported: true,
				get_editor: function (){
					var elm = null, win = null, editor = {element: null, property: null, type: null, win: null};

					try
					{

						if( document.getElementById('write_content_ifr') )
						{
							elm = document.getElementById('write_content_ifr');
						}

						if (elm && elm.contentWindow)
						{
							win = elm.contentWindow;
							elm = null;
						}
						else
						{
							elm = document.getElementById('write_content');
						}

						editor = win && {element: win.document.body, property: 'innerHTML', type: 'RTE', win: win} ||
							elm && {element: elm, property: 'value', type: elm.tagName.toLowerCase(), win: null} ||
							editor;
					}
					catch (er)
					{
						$.zemanta.log(er);
					}

					return editor;
				}
			}
		});
	}

	function waitForLoad()
	{
		var done = false, t0 = null;

		if (typeof $.zemanta === "undefined")
		{
			$('#zemanta-message').html('Waiting...');
			setTimeout(arguments.callee, 100);
			return;
		}
		t0 = arguments.callee.t0 = arguments.callee.t0 || new Date().getTime();

		$('#zemanta-message').html('Initializing...');

		try
		{
			done = $.zemanta.initialize(setPlatform($, { interface_type: "easyblog" } ) );
		}
		catch (er)
		{
			done = true;
		}

		if (!done) {
			if (new Date().getTime() - t0 < 15000) {
				setTimeout(arguments.callee, 100);
			} else {
				$('#zemanta-message').html('Gave up on finding editor. ').append($('<a href="#">Retry</a>').click(arguments.callee));
			}
		}
	}

	try
	{
		$ = window.zQuery;

		if (!$)
		{
			throw "No zQuery!";
		}

		if ($('#zemanta-message').html() === 'Loading...')
		{
			$('#zemanta-message').html('Preparing...');
		}
		waitForLoad();
	}
	catch (er)
	{
		window.setTimeout(arguments.callee, 100);
		return;
	}
})();
