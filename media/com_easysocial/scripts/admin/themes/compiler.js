EasySocial.module("admin/themes/compiler", function($){

var module = this;

// TODO: Move this away
$.fn.at = function(key) {
	return this.find("[data-" + key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase() + "]");
}

$.template("easysocial/compiler/detail", '<tr class="[%= type %]"><td>[%= timestamp %]</td><td width="100%">[%= message %]</td></tr>');

EasySocial.Controller("Themes.Compiler", {

	defaultOptions: {

		view: {
			detail: "compiler/detail"
		},

		"{section}": "[data-section]",

		"{compileButton}"     : "[data-compile-button]",
		"{minifyButton}"      : "[data-minify-button]",
		"{buildButton}"       : "[data-build-button]",
		"{filesButton}"       : "[data-files-button]",
		"{purgeButton}"       : "[data-purge-button]",
		"{resetButton}"       : "[data-reset-button]",
		"{forceCompileButton}": "[data-force-compile-button]",
		"{clearLogButton}"    : "[data-clear-log-button]",
		"{toggleLogButton}"   : "[data-toggle-log-button]",

		"{refreshButton}": "[data-refresh-button]",
		"{refreshSectionButton}": "[data-refresh-section-button]",
		"{buildWithoutMinifyButton}": "[data-build-without-minify-button]",

		"{log}"        : "[data-log]",
		"{details}"    : "[data-details]",
		"{status}"     : "[data-status]",
		"{imports}"    : "[data-imports]",

		"{progress}"      : "[data-progress]",
		"{progressBar}"   : "[data-progress-bar]",
		"{progressStatus}": "[data-progress-status]",

		"{tabItem}": ".tab-item"
	}
},
function(self, opts, base) { return {

	init: function() {

		self.location = base.data("location");
		self.name     = base.data("name");
		self.override = base.data("override");
	},

	sections: function() {

		var sections =
			$.map(self.section(), function(section) {

				var name = $(section).data("sectionName"),
					id   = self.sectionId(name);

				return {id: id, name: name};
			});

		return sections;
	},

	sectionId: function(sectionName) {

		return self.location + '-' + self.name + '-' + sectionName;
	},

	sectionTab: function(sectionName) {

		return self.tabItem({"sectionId": self.sectionId(sectionName)}).find("> a");
	},

	sectionNameOf: function(el) {

		var section = self.section.of(el),
			sectionName = section.data("sectionName");

		return sectionName;
	},

	getLog: function(sectionName) {

		if (sectionName) return self.section({"sectionName": sectionName}).at("log");
		return self.log(":first");
	},

	addLog: function(detail, sectionName) {

		var log = self.getLog(sectionName),
			body = log.find("tbody");

		// Normalize arguments
		if ($.isString(detail)) {
			detail = {
				timestamp: new Date(),
				message: detail,
				type: "info"
			}
		}

		// Type
		var type = detail.type;
		if (/warning|warn/.test(type)) type = 'warning';
		if (/danger|error|failed|fail/.test(type)) type = 'danger';
		if (/pending|default|primary/.test(type)) type = '';

		// Timestamp
		var timestamp = detail.timestamp,
			date = (timestamp instanceof Date) ? timestamp : new Date(parseFloat(timestamp));

		// Convert timestamp to h:i:s
		timestamp = date.getHours() + ':' + date.getMinutes() + ":" + date.getSeconds();

		// Message
		var message = detail.message;

		self.view.detail({
				type: type,
				timestamp: timestamp,
				message: message
			})
			.appendTo(body);

		body[0].scrollTop = body[0].scrollHeight;
	},

	appendLog: function(details, sectionName) {

		$.each(details, function(key, detail){
			self.addLog(detail, sectionName);
		});
	},

	clearLog: function(sectionName) {

		var log = self.getLog(sectionName),
			body = log.find("tbody");

		body.empty();

		log.at("timeTotal").data("value", 0).html("0s");
		log.at("memoryUsage").data("value", 0).html("0mb");
	},

	generateLog: function(task, sectionName) {

		self.clearLog(sectionName);

		self.appendLog(task.details, sectionName);

		var log = self.getLog(sectionName),
			timeTotalValue = parseFloat(task.time_total),
			timeTotalText  = timeTotalValue.toFixed(2) + 's',
			memoryUsageValue = parseInt(task.mem_peak),
			memoryUsageText  = (memoryUsageValue / 1024 / 1024).toFixed(2) + 'mb';

		log.at("timeTotal")
			.data("value", timeTotalValue)
			.html(timeTotalText);

		log.at("memoryUsage")
			.data("value", memoryUsageValue)
			.html(memoryUsageText);

		self.addLog({
			type: (task.failed) ? "danger" : "success",
			timestamp: task.time_end,
			message: (task.failed) ? "Task failed." : "Task completed.",
		}, sectionName);
	},

	updateLog: function(task, sectionName) {

		var log = self.getLog(sectionName),
			timeTotal = log.at("timeTotal"),
			memoryUsage = log.at("memoryUsage");

		// Append log
		self.appendLog(task.details, sectionName);

		// Refactor this. Copied code from above.
		var timeTotalValue = timeTotal.data("value") + parseFloat(task.time_total),
			timeTotalText  = timeTotalValue.toFixed(2) + 's',
			memoryUsageValue = Math.max(memoryUsage.data("value"), parseInt(task.mem_peak)),
			memoryUsageText  = (memoryUsageValue / 1024 / 1024).toFixed(2) + 'mb';

		timeTotal
			.data("value", timeTotalValue)
			.html(timeTotalText);

		memoryUsage
			.data("value", memoryUsageValue)
			.html(memoryUsageText);
	},

	perform: function(task, options) {

		return EasySocial.ajax(
				"admin/controllers/themes/" + task,
				$.extend({
					location: self.location,
					name: self.name,
					override: self.override
				}, options));
	},

	build: function(preset) {

		var sections = self.sections(),

			preset = preset || 'cache',

			minify = (preset=='cache'),

			// To determine when to stop running tasks
			i = 0,
			length = sections.length - 1,

			// To determine progress bar width
			current = i + 1,
			total = ((length + 1) * (minify ? 2 : 1)) + 1, // one more for building
			max = 100,

			// To determine if there was a failure
			failed = false,

			log = self.log(":first"),

			progress       = self.progress(":first"),
			progressBar    = self.progressBar(":first"),
			progressStatus = self.progressStatus(":first"),

			updateProgress = function() {

				current++;

				// Update progress bar
				progressValue = current / total * max;
				progressBar.show().width(progressValue + "%");
			},

			run = function() {

				var section     = sections[i],
					sectionId   = section.id,
					sectionName = section.name,
					sectionTab  = self.sectionTab(sectionName);

				// Message
				var message = "Compiling section '" + sectionName + "'.";

				// Update progress bar
				updateProgress();

				// Update compile status
				progressStatus.html(message);

				// Show section tab content
				sectionTab.tab("show");

				self.compile(sectionName, {force: true})
					.done(function(data){
						self.updateLog(data.task);
					})
					.fail(function(data){
						self.updateLog(data.task);
						failed = true;
					})
					.always(function(){

						if (!minify) {
							nextSection();
						} else {

							// Message
							var message = "Minifying section '" + sectionName + "'.";

							// Update progress bar
							updateProgress();

							// Update compile status
							progressStatus.html(message);

							self.minify(sectionName)
								.done(function(data){
									self.updateLog(data.task);
								})
								.fail(function(data){
									self.updateLog(data.task);
									failed = true;
								})
								.always(function(){
									nextSection();
								});
						}
					});
			},

			nextSection = function() {

				// Compile next section
				if (length > i++) return run();

				// If all sections have been compiled, build.
				build();
			},

			build = function() {

				progressStatus.html("Building stylesheets.");
				updateProgress();

				var task;

				self.perform("build", {preset: preset})
					.done(function(data){
						self.status(":first").replaceWith(data.status);
						task = data.task;
					})
					.fail(function(data){
						task = data.task;
					})
					.always(function(){

						// Fallback pseudo task object.
						if (!task) {
							task = {
								failed: true,
								details: [],
								time_end: new Date()
							};
						}

						// Update log
						self.updateLog(task);

						self.addLog({
							type: (task.failed) ? "danger" : "success",
							timestamp: task.time_end,
							message: (task.failed) ? "Build failed." : "Build completed.",
						});

						progressStatus.html("Build completed!");
						progressBar.width("100%");

						// Hide compiler progress
						base.removeClass("is-busy");
					});
			};

		// Reset progress bar
		progressBar.hide().width("0%");

		// Show compiler progress
		base.addClass("is-busy");

		// Log
		self.clearLog();

		if (sections.length > 0) {
			self.addLog("Compiling all sections.");
			// Compile section
			run();
		} else {
			build();
		}
	},

	minify: function(sectionName, options) {

		var task = self.perform("minify", $.extend({section: sectionName}, options));

		self.trigger("minify", [sectionName, task]);

		return task;
	},

	"{self} minify": function(base, event, sectionName, task) {

		var section = self.section({"sectionName": sectionName}),
			progressStatus = section.at("progressStatus"),
			progressBar = section.at("progressBar"),
			message = "Minifying section '" + sectionName + "'.";

		// Progress bar
		section.addClass("is-busy");
		progressStatus.html(message);
		progressBar.hide().width("0%").show().width("100%");

		// Log
		// self.clearLog(sectionName);
		// self.addLog(message, sectionName);

		task
			.done(function(data){

				// Update imports & status html
				section.at("imports").replaceWith(data.imports);
				section.at("status").replaceWith(data.status);

				// Generate log
				self.appendLog(data.task.details, sectionName);

				self.addLog({
					type: (task.failed) ? "danger" : "success",
					timestamp: data.task.time_end,
					message: (task.failed) ? "Task failed." : "Task completed.",
				}, sectionName);
			})
			.always(function(){
				section.removeClass("is-busy");
			});
	},

	compile: function(sectionName, options) {

		var task = self.perform("compile", $.extend({section: sectionName}, options));

		self.trigger("compile", [sectionName, task]);

		return task;
	},

	"{self} compile": function(base, event, sectionName, task) {

		var section = self.section({"sectionName": sectionName}),
			progressStatus = section.at("progressStatus"),
			progressBar = section.at("progressBar"),
			message = "Compiling section '" + sectionName + "'.";

		// Progress bar
		section.addClass("is-busy");
		progressStatus.html(message);
		progressBar.hide().width("0%").show().width("100%");

		// Log
		self.clearLog(sectionName);
		self.addLog(message, sectionName);

		task
			.done(function(data){

				// Update imports & status html
				section.at("imports").replaceWith(data.imports);
				section.at("status").replaceWith(data.status);

				// Generate log
				self.generateLog(data.task, sectionName);
			})
			.fail(function(data){

				// Generate log
				self.generateLog(data.task, sectionName);
			})
			.always(function(){

				section.removeClass("is-busy");
			});
	},

	purge: function() {

		var progress       = self.progress(":first"),
			progressBar    = self.progressBar(":first"),
			progressStatus = self.progressStatus(":first");

		base.addClass("is-busy");

		self.clearLog();

		progressBar.hide().width("0%").show().width("100%");
		progressStatus.html("Purging cache and log files.");

		self.perform("purge")
			.done(function(task){

				self.generateLog(task);
			})
			.fail(function(task){

			})
			.always(function(){
				base.removeClass("is-busy");
			});
	},

	"{buildButton} click": function(buildButton) {

		self.build('cache');
	},

	"{buildWithoutMinifyButton} click": function() {

		self.build('development');
	},

	"{compileAllButton} click": function(compilceAllButton) {

		// self.compileAllSections();
	},

	"{compileButton} click": function(compileButton) {

		var sectionName = self.sectionNameOf(compileButton);
		return self.compile(sectionName);
	},

	"{forceCompileButton} click": function(forceCompileButton) {

		var sectionName = self.sectionNameOf(forceCompileButton);
		return self.compile(sectionName, {force: true});
	},

	"{minifyButton} click": function(minifyButton) {

		var sectionName = self.sectionNameOf(minifyButton);
		return self.minify(sectionName);
	},

	"{refreshButton} click": function() {

		// Show loading indicator
		base.addClass("is-busy");

		// Show indefinite progress bar
		self.progressBar(":first").show().width("100%");

		// Update status text
		self.progressStatus(":first").html("Refreshing");

		EasySocial.ajax("admin/views/themes/compiler",
		{
			location: self.location,
			name: self.name,
			override: self.override
		})
		.done(function(html){

			base.replaceWith(html);
		})
		.fail(function(){
			alert("Unable to refresh section.");
		})
		.always(function(){
			base.removeClass("is-busy");
		});
	},

	"{refreshSectionButton} click": function(refreshSectionButton) {

		var sectionName = self.sectionNameOf(refreshSectionButton),
			section = self.section({"sectionName": sectionName});

		// Show loading indicator
		section.addClass("is-busy");

		// Update status text
		section.at("progressStatus").html("Refreshing");

		EasySocial.ajax("admin/views/themes/section",
		{
			location: self.location,
			name: self.name,
			override: self.override,
			section: sectionName
		})
		.done(function(html){
			section.html(html);
		})
		.fail(function(){
			// TODO: Nicer alert.
			alert("Unable to refresh section.");
		})
		.always(function(){
			section.removeClass("is-busy");
		});
	},

	"{resetButton} click": function() {

		alert("TODO: Restore to factory default.");
	},

	"{purgeButton} click": function() {

		self.purge();
	}
}});

module.resolve();

});