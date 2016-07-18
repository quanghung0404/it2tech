EasySocial.module( 'admin/grid/grid' , function($) {

	var module = this;

	EasySocial.require()
	.script( 'admin/grid/sort' , 'admin/grid/publishing')
	.done(function($)
	{
		EasySocial.Controller(
			'Grid',
			{
				defaultOptions :
				{
					"{sortColumns}"		: "[data-table-grid-sort]",
					"{ordering}"		: "[data-table-grid-ordering]",
					"{saveorder}"		: "[data-table-grid-saveorder]",
					"{direction}"		: "[data-table-grid-direction]",

					"{task}"			: "[data-table-grid-task]",

					"{searchInput}"		: "[data-table-grid-search-input]",
					"{search}"			: "[data-table-grid-search]",
					"{resetSearch}"		: "[data-table-grid-search-reset]",

					"{checkAll}"		: "[data-table-grid-checkall]",
					"{checkboxes}"		: "[data-table-grid-id]",

					"{publishItems}"	: "[data-table-grid-publishing]",

					"{itemRow}"			: "tr",

					"{boxChecked}"		: "[data-table-grid-box-checked]",
					"{filters}"			: "[data-table-grid-filter]"
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						// Implement sortable items.
						self.implementSortable();

						// Implement publish / unpublish
						self.implementPublishing();
					},

					"{filters} change" : function()
					{
						// Always reset the task before submitting.
						self.setTask( '' );

						self.submitForm();
					},

					"{search} click" : function()
					{
						self.submitForm();
					},

					"{saveorder} click" : function()
					{
						self.setTask('saveorder');

						// check all checkbox.
						self.checkAll().click();
						self.submitForm();
					},

					"{resetSearch} click" : function()
					{
						self.searchInput().val( '' );
						self.submitForm();
					},

					submitForm: function()
					{
						self.element.submit();
					},

					setTask: function( task )
					{
						self.task().val( task );
					},

					setOrdering: function( ordering )
					{
						self.ordering().val( ordering );
					},

					setDirection: function( direction )
					{
						self.direction().val( direction );
					},

					setTotalChecked: function( total )
					{
						self.boxChecked().val( total );
					},

					toggleSelectRow: function( row )
					{
						var checkbox 	= row.find( 'input[name=cid\\[\\]]' );

						if( $( checkbox ).prop( 'checked' ) == true )
						{
							$( checkbox ).prop( 'checked' , false );
						}
						else
						{
							$( checkbox ).prop( 'checked' , true );
						}

					},
					selectRow: function( row )
					{
						var checkbox 	= row.find( 'input[name=cid\\[\\]]' );

						$( checkbox ).prop( 'checked' , true );
					},

					implementSortable: function()
					{
						self.sortColumns().implement( EasySocial.Controller.Grid.Sort ,
						{
							"{parent}" 	: self
						});
					},

					implementPublishing: function()
					{
						self.publishItems().implement( EasySocial.Controller.Grid.Publishing,
						{
							"{parent}"	: self
						});
					},

					"{checkAll} change": function( element , event )
					{
						// Find all checkboxes in the grid.
						self.checkboxes().prop( 'checked' , $( element ).is( ':checked' ) );

						// Update the total number of checkboxes checked.
						var total 	= $( element ).is( ':checked' ) ? self.checkboxes().length : 0;


						self.setTotalChecked( total );
					}
				}
			}
		);

		module.resolve();
	});


});
