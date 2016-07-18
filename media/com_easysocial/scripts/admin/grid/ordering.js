EasySocial.module( 'admin/grid/ordering' , function($) {

	var module = this;

	EasySocial.Controller('Grid.Ordering', {
		
		defaultOptions: {
			"{moveUp}": "[data-grid-order-up]",
			"{moveDown}": "[data-grid-order-down]",
			row: null
		}
	}, function(self) {
		return {

			init : function() {
				// Get the parent row
				self.options.row = self.element.parents( 'tr' );
			},

			selectRow : function() {
				var checkbox = self.options.row.find('input[name=cid\\[\\]]' );

				// Ensure that the checkbox is checked
				$(checkbox).prop('checked', true);
			},

			"{moveUp} click" : function() {
				self.selectRow();

				$.Joomla('submitform', ['moveUp']);
			},

			"{moveDown} click" : function() {
				self.selectRow();

				$.Joomla('submitform', ['moveDown']);
			}
		}
	});
		
	module.resolve();

});