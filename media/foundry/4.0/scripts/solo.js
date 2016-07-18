FD40.plugin("solo", function($) {

(function(){

var WebSockets = window.WebSocket || window.MozWebSocket,
    doco = $(document);

var solo = window.solo = {

    socket: null,

    defaultUrl: 'ws://localhost:7788',

    connect: function() {

        // Close existing connection
        solo.disconnect();

        doco.trigger("soloInit", arguments);

        // Create a new web socket to solo scanner
        var socket = solo.socket = new WebSockets(solo.defaultUrl);

        // Bind messages
        socket.onmessage = function(event) {
            doco.trigger("soloMessage", arguments);
            solo.parse(event.data);
        }

        socket.onopen = function() {
            doco.trigger("soloOpen", arguments);
        }

        socket.onclose = function() {
            doco.trigger("soloClose", arguments);
        }

        socket.onerror = function() {
        	doco.trigger("soloError", arguments);
        }
    },

    disconnect: function() {

        if (solo.socket) {
            solo.socket.close();
        }
    },

    states: ['CONNECTING', 'CONNECTED', 'DISCONNECTING', 'DISCONNECTED'],

    state: function() {

        if (!solo.socket) {
        	return 'IDLE'
        }

        var state = solo.socket.readyState;

        return self.states[state] || "UNKNOWN STATE " + state;
    },

    send: function() {
        solo.socket.send(JSON.stringify($.makeArray(arguments)));
    },

    parse: function(data) {

    	var command = JSON.parse(data),
    		method = solo.commands[command[0]];

    	method && method.apply(null, command[1]);
    },

    watching: false,

	watch: function(name) {

		solo.disconnect();

		solo.watching = true;

		$.Storage.set("solo.watch", true);

		doco.off("soloOpen.watch soloClose.watch soloError.watch")
			.on("soloOpen.watch", function(){
				console.info("[SOLO WATCH] Connected to server.\nRun solo.unwatch() to disable watching.");
			})
			.on("soloClose.watch soloError.watch", $.debounce(function(){
				console.warn("[SOLO WATCH] Disconnected or unable to connect to server.\nRun solo.watch() to reconnect or manually refresh the page.");
			}, 50));

		// If solo is not connected, connect now.
		solo.connect();
	},

	unwatch: function() {

		self.watching = false;

		$.Storage.set("solo.watch", false);

		doco.off("soloOpen.watch soloClose.watch soloError.watch");
	},

	commands: {

		refresh: function() {

			if (solo.watching) {
				solo.send("inform", ["Refreshing " + document.location.href + " on " + navigator.userAgent + "."]);
				document.location.reload();
			}
		}
	}
}

if (solo.watching = $.Storage.get("solo.watch")) {
	solo.watch();
}

})();

});