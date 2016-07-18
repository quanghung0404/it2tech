// module: start
EasyBlog.module("dashboard/editor", function($){

var module = this;

EasyBlog.Controller(
	"Dashboard.Editor",
	{
		defaultOptions: {

			editorId: "write_content"
		}
	},
	function(self) { return {

		init: function() {

			EasyBlog.dashboard.registerPlugin("editor", self);
		},

		insert: function(html) {

			window.jInsertEditorText(html, self.options.editorId);
		},

		content: function(html) {

			// TODO: Port eblog.editor to this new controller

			if (html!==undefined) {

				window.eblog.editor.setContent(html);
			}

			return window.eblog.editor.getContent();
		}
	}}
);

module.resolve();

});
