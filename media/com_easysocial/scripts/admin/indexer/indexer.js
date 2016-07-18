EasySocial.module( 'admin/indexer/indexer' , function($){

	var module	= this;

	EasySocial.require()
	.language(
		'COM_EASYSOCIAL_INDEXER_REINDEX_PROCESSING',
		'COM_EASYSOCIAL_INDEXER_REINDEX_FINISHED',
		'COM_EASYSOCIAL_INDEXER_REINDEX_RESTART'
		)
	.view( 'site/loading/small' )
	.done(function($){

		EasySocial.Controller(
		'Indexer',
		{
			defaultOptions:
			{
				// Elements
				"{startButton}"	: "[data-start-button]",
				"{indexerBar}" : "[data-indexer-bar]",
				"{indexerResult}" : "[data-indexer-result]",
				"{indexerMessage}" : "[data-indexer-message]",
				"{resultsButton}"	: "[data-results-button]",

				view :
				{
					loadingContent 	: "site/loading/small"
				}
			}
		},
		function( self ){
			return {

				init : function(){},

				"{startButton} click" : function()
				{
					self.runIndex( 0 );
					self.indexerMessage().html( $.language('COM_EASYSOCIAL_INDEXER_REINDEX_PROCESSING') );
					self.indexerMessage().show();
					self.startButton().hide();
				},

				runIndex : function( max ){

					//ajax call here.
					EasySocial.ajax( 'admin/controllers/indexer/indexing',
					{
						"max" 		: max,

					},
					{
						beforeSend: function()
						{
							// self.startButton().html( self.view.loadingContent() );
						}
					})
					.done(function( max, progress )
					{
						if( max < 0 )
						{
							progress = '100';
						}

						self.updateProgress( progress );

						if( max >= 0)
						{
							self.runIndex( max );
						}

					})
					.fail(function( message ){
						self.setMessage( message );
					})
					.always(function(){

					});


				},

				updateProgress: function( progress )
				{
					self.indexerBar().css( 'width', progress + '%')
					self.indexerResult().html( progress + '%' );

					if( progress == 100 )
					{
						self.indexerMessage().html( $.language( 'COM_EASYSOCIAL_INDEXER_REINDEX_FINISHED' ) );
						self.startButton().html( $.language( 'COM_EASYSOCIAL_INDEXER_REINDEX_RESTART' ) );
						self.startButton().show();

						self.resultsButton().removeClass('hide');
					}
				},



			}
		});

		module.resolve();
	});

});
