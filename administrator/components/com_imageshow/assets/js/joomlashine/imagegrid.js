(function($){
	/**
	 * Image
	 */
	$.JSNISImageGrid = function( options ){
		var ImageGrid             = this;
		/**
		 * Class of HTML element, class of element images listing
		 *
		 */
		ImageGrid.classImagesSort = '.items';
		ImageGrid.receive 		  = false;
		/**
		 * Class of HTML element, class for element multiple
		 */
		ImageGrid.classMultiple   = '.media-item-multiple-select';
		/**
		 * jQuery element, parent of source images listing
		 */
		ImageGrid.sourceImages    = $('#source-items');
		/**
		 * jQuery element, parent of showlist images
		 */
		ImageGrid.showlistImages  = $('#showlist-items');
		/**
		 * Delete images showlist button
		 */
		ImageGrid.deleteImageShowlistBtt   = $('#delete-media-showlist');
		/**
		 * Edit image showlist button
		 */
		ImageGrid.editImageShowlistBtt     = $('#edit-media-showlist');
		/**
		 * Total images from external source
		 */
		ImageGrid.imageTotal	=	-1;
		/**
		 * Save button
		 */
		ImageGrid.applyButton	= $('#toolbar-apply').children('a');
		/**
		 * Save and close button
		 */
		ImageGrid.saveButton	= $('#toolbar-save').children('a');
		/**
		 *	Click on tree icon
		 */
		ImageGrid.clickTreeIcon	=	false;
		/**
		 * Number of generated thumbnail
		 * 
		 */
		ImageGrid.generatedThumbnailNumber = 0;
		/**
		 * Select link image popup
		 */
		ImageGrid.selectlinkBtt     = $('#select-link');
		/**

		 * Move image to showlist button
		 */
		ImageGrid.moveImageToShowlistBtt   = $('#move-selected-media-source');
		/**
		 * Header of source images
		 */
		ImageGrid.sourcePanelHeader        = $('#source-media-header');
		/**
		 * Header of showlist
		 */
		ImageGrid.showlistPanelHeader      = $('#showlist-media-header');
		/**
		 * Header of tree control
		 */
		ImageGrid.treePanelHeader          = $('#jsn-header-tree-control');
		/**
		 * Variable to store JSN jTree
		 */
		ImageGrid.jsnjTree;
		/**
		 * Make option object for script
		 */
		ImageGrid.options         = $.extend({}, options);
		/**
		 * Variable UILayout
		 */
		ImageGrid.UILayout;
		/**
		 * Object variables rate of layout
		 */
		ImageGrid.resizeRate;
		/**
		 * Object variables store UILayout panel
		 */
		ImageGrid.freeImageLimit = 10;

		ImageGrid.panels          = {
			/**
			 * jQuery element for init UILayout
			 */
			panelFull   : $('#jsn-showlist-media-layout'),
			/**
			 * jQuery element west layout
			 */
			panelWest   : $('#panel-west'),
			/**
			 * jQuery element center layout
			 */
			panelCenter : $('#panel-center')
		};
		/**
		 * Object jQuery variables content of grid
		 */
		ImageGrid.contents        = {
			/**
			 * jQuery element tree categories of image
			 */
			categories     : $('#jsn-jtree-categories'),
			/**
			 * jQuery element source image container
			 */
			sourceimages   : $('#source-media-container'),
			/**
			 * jQuery element showlist image container
			 */
			showlistimages : $('#showlist-media-container')
		};
		/**
		 * Cookie store
		 */
		ImageGrid.cookie          = {
			set    : function(name, value){
				$.cookie( name, value );
			},
			get    : function( name, type ){
				switch(type){
					case 'int'  :
						return parseInt( $.cookie(name) );
					case 'float' :
						return parseFloat( $.cookie(name) );
					default:
						return $.cookie( name );
				}
			},
			exists : function(name){
				return $.cookie( name ) == null ? false : true;
			}
		};
		/**
		 * Initialize image grid
		 */
		ImageGrid.initialize      = function(){

			//ImageGrid.repaddingImage();

			/*ImageGrid.showlistImages.find('div.media-item').each(function(){
				    var src = $(this).find('#linkcheck').val();
				    var http = $.ajax({
					    type:"HEAD",
					    url: src,
					    async: false
					  })
				   var check = http.status;
				   if(check==404){
				   		$(this).find('.item_link').addClass('noimage');
				   		//$(this).find('.item_link img').attr('src','aa');
				   		$(this).find('.item_link img').remove();
				   		$(this).find('.item_link').append('<img src="" style="max-height: 60px; max-width: 80px; padding-top: 30px !important; border: none !important;" />');
				   }
			});*/
			if ( ImageGrid.options.selectMode == 'sync' ){
				$.when(
					ImageGrid.initEvents()
				).then(
					$.when(
						// show images the first album choosed

						//showLoading(),
						ImageGrid.syncRefreshing()
					).then(
						showLoading({removeall:true})
					)
				);
			}else if(ImageGrid.options.removeload == 1){
					showLoading({removeall:true});
			}else{
				$.when(
					//showLoading(false),
					//showLoading({removeall:false}),
					ImageGrid.initEvents()
				).then(
					//showLoading({removeall:true})
				);
			}
		};

		ImageGrid.SelectAllImages = function(val){
			if(val=='source'){
				ImageGrid.sourceImages.find('div.media-item').each(function(){
					ImageGrid.multipleselect.select($(this) );
				});
				ImageGrid.moveImageToShowlistBtt.children('i').removeClass('disabled');
				ImageGrid.moveImageToShowlistBtt.unbind("click").bind("click");
				//ImageGrid.moveImageToShowlistBtt.parent().removeClass('disabled');
			}else{
				ImageGrid.showlistImages.find('div.media-item').each(function(){
					ImageGrid.multipleselect.select($(this) );
				});
				ImageGrid.editImageShowlistBtt.children('i').removeClass('disabled');
				//ImageGrid.editImageShowlistBtt.parent().removeClass('disabled');
				ImageGrid.deleteImageShowlistBtt.children('i').removeClass('disabled');
				//ImageGrid.deleteImageShowlistBtt.parent().removeClass('disabled');
			}
			ImageGrid.activeButtonsAction();
		};

		ImageGrid.DeselectAll 	  = function(val){
			if(val=='source'){
				ImageGrid.multipleselect.deSelectAll(ImageGrid.sourceImages);
				ImageGrid.moveImageToShowlistBtt.children('i').addClass('disabled');
				ImageGrid.moveImageToShowlistBtt.unbind("click");
				//ImageGrid.moveImageToShowlistBtt.parent().addClass('disabled');
			}else{
				ImageGrid.multipleselect.deSelectAll(ImageGrid.showlistImages);
				ImageGrid.editImageShowlistBtt.children('i').addClass('disabled');
				//ImageGrid.editImageShowlistBtt.parent().addClass('disabled');
				ImageGrid.deleteImageShowlistBtt.children('i').addClass('disabled');
				//ImageGrid.deleteImageShowlistBtt.parent().addClass('disabled');
			}
		}

		ImageGrid.RevertSelection = function(val){
			if(val=='source'){
				ImageGrid.sourceImages.find('div.media-item').each(function(){
					if($(this).hasClass('media-item-multiple-select')){
						$(this).removeClass('media-item-multiple-select');
					}else{
						ImageGrid.multipleselect.select($(this) );
					}
				});
				var count = ImageGrid.sourceImages.find('.media-item-multiple-select').length;
				if(parseInt(count) > 0){
					ImageGrid.moveImageToShowlistBtt.children('i').removeClass('disabled');
					//ImageGrid.moveImageToShowlistBtt.unbind("click").bind("click");
					//ImageGrid.moveImageToShowlistBtt.parent().removeClass('disabled');
				}else{
					ImageGrid.moveImageToShowlistBtt.children('i').addClass('disabled');
					//ImageGrid.moveImageToShowlistBtt.unbind("click");
					//ImageGrid.moveImageToShowlistBtt.parent().addClass('disabled');
				}
			}else{
				ImageGrid.showlistImages.find('div.media-item').each(function(){
					if($(this).hasClass('media-item-multiple-select')){
						$(this).removeClass('media-item-multiple-select');
					}else{
						ImageGrid.multipleselect.select($(this) );
					}
				});
				var count = ImageGrid.showlistImages.find('.media-item-multiple-select').length;
				if(parseInt(count) > 0){
					ImageGrid.editImageShowlistBtt.children('i').removeClass('disabled');
					//ImageGrid.editImageShowlistBtt.parent().removeClass('disabled');
					ImageGrid.deleteImageShowlistBtt.children('i').removeClass('disabled');
					//ImageGrid.deleteImageShowlistBtt.parent().removeClass('disabled');
				}else{
					ImageGrid.editImageShowlistBtt.children('i').addClass('disabled');
					//ImageGrid.editImageShowlistBtt.parent().addClass('disabled');
					ImageGrid.deleteImageShowlistBtt.children('i').addClass('disabled');
					//ImageGrid.deleteImageShowlistBtt.parent().addClass('disabled');
				}

			}
		}

		ImageGrid.removecatSelected  = function(){
			ImageGrid.contents.categories.find('ul li').each(function(){
							$(this).addClass('catsyn');
						});
		}

		ImageGrid.repaddingImage = function(){
			ImageGrid.sourceImages.find('div.item-thumbnail img').each(function(){
				 $(this).load(function() {
					var imageHeight 	= $(this).height();
					var parentheight    = $('div.item-thumbnail').height();
					var padding = parentheight/2 - imageHeight/2-5;
					$(this).css('padding-top',padding);
				 })
			});
			ImageGrid.showlistImages.find('div.item-thumbnail img').each(function(){
				 $(this).load(function() {
					var imageHeight 	= $(this).height();
					var parentheight    = $('div.item-thumbnail').height();
					var padding = parentheight/2 - imageHeight/2-5;
					$(this).css('padding-top',padding);
				});
			});
		}

		/**
		* Reset Detail Images
		*/
		ImageGrid.ResetDetailImages = function(){
			var count = ImageGrid.showlistImages.find('.media-item-multiple-select').length;
			if(count>0){
				// ajax reset detail of images.
				ImageGrid.showlistImages.find('.media-item-multiple-select').each(function(){
					$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=image&task=resetImageDetails', {
						showlist_id : ImageGrid.options.showListID,
						image_extid : $(this).attr('id'),
						album_extid	: $(this).find('.item-info').attr('id'),
						img_detail  : $(this).find('input.item_detail').val()
					}).success(function(res){

					})
					$(this).find('div.modified').removeClass('modified');
				});
			}else{
				$("#dialogbox").html('<div style="width:100%; text-align:center; font-size:16px; font-weight:500; margin-top:30px; ">No item is selected</div>').dialog(
								{
									width: 600,
									modal: true,
									title: '<span style="font-size: 15px; font-weight:bold;">Confirmation</span>',
									buttons: [
								    {
								        text: "Close",
								        click: function() { $(this).dialog("close"); }
								    }
									]
								});
			}
		}

		/**
		* Purge Absolete Images
		*/
		ImageGrid.PurgeAbsoleteImages = function(){

				// process reset detail of each images is selected
				ImageGrid.showlistImages.find('div.media-item, div.media-item-multiple-select').each(function(){
				    var src = $(this).find('#linkcheck').val();
				    var http = $.ajax({
					    type:"HEAD",
					    url: src,
					    async: false
					  })
				   var check = http.status;
				   if(check ==404){
				   		$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=image&task=PurgeAbsoleteImages', {
							showListID : ImageGrid.options.showListID,
							ImageID    : $(this).attr('id')
						}).success(function(res){

						})
						$(this).fadeOut(1500,function(){
							ImageGrid.indexImages();
							ImageGrid.contentResize();
							ImageGrid.multipleselect.init();
							$(this).remove();
							$('div[id="'+$(this).attr('id')+'"]', ImageGrid.sourceImages).each(function(){
								$(this).removeClass('media-item-is-selected').addClass('media-item');
							});
							ImageGrid.removeselectedAlbum();
						});
				   }
				});

		}
		/**
		 * Set options
		 */
		ImageGrid.setOption       = function(name, value){
			if ( typeof name == 'Array' ){
				for(k in name){
					ImageGrid.options[name] = value;
				}
			}else{
				if ( ImageGrid.options[name] != undefined ){
					ImageGrid.options[name] = value;
				}
			}
		};
		/**
		*
		* Init layout
		*
		* @return: None
		*/
		ImageGrid.initLayout      = function(){
			ImageGrid.panels.panelFull.css('width', ImageGrid.options.layoutWidth);
			ImageGrid.panels.panelFull.css('height', ImageGrid.options.layoutHeight);
			ImageGrid.panels.panelFull.css('z-index', 0);
			ImageGrid.contents.categories.css('height', ImageGrid.options.layoutHeight - 115);
			ImageGrid.contents.sourceimages.css('height', ImageGrid.options.layoutHeight - 95);
			ImageGrid.contents.showlistimages.css('height', ImageGrid.options.layoutHeight - 95);

			ImageGrid.UILayout = ImageGrid.panels.panelFull.layout({
				west__onresize: function(){
					ImageGrid.contentResize();
				},
				west__onopen: function(){
					ImageGrid.contentResize();
				},
				west__onclose: function(){
					if ( ImageGrid.sourceImages.hasClass('showlist')){
						//ImageGrid.sourceImages.find('div.media-item').removeAttr('style');
						//ImageGrid.sourceImages.find('div.media-item-is-selected').removeAttr('style');
					}
					if ( ImageGrid.showlistImages.hasClass('showlist')){
						ImageGrid.showlistImages.find('div.media-item').removeAttr('style');
					}
					ImageGrid.contentResize();
				},
				onresizeall_end: function(){
					setTimeout(function(){
						ImageGrid.calculatorRate();
						if ( ImageGrid.sourceImages.hasClass('showlist')){
							//ImageGrid.sourceImages.find('div.media-item').removeAttr('style');
							//ImageGrid.sourceImages.find('div.media-item-is-selected').removeAttr('style');
						}

						if ( ImageGrid.showlistImages.hasClass('showlist')){
							ImageGrid.showlistImages.find('div.media-item').removeAttr('style');
						}
						ImageGrid.contentResize();
					}, 200);
				},
				ondrag_end: function(){
					setTimeout(function(){
						ImageGrid.calculatorRate();
						if ( ImageGrid.sourceImages.hasClass('showlist')){
							//ImageGrid.sourceImages.find('div.media-item').removeAttr('style');
							//ImageGrid.sourceImages.find('div.media-item-is-selected').removeAttr('style');
						}

						if ( ImageGrid.showlistImages.hasClass('showlist')){
							ImageGrid.showlistImages.find('div.media-item').removeAttr('style');
						}
						ImageGrid.contentResize();
						ImageGrid.cookie.set('rate_of_west', ImageGrid.resizeRate.west );
					}, 200);
				}
			});
			ImageGrid.panels.panelWest.css('position', '');
			if ( $.browser.msie ){
				ImageGrid.contents.showlistimages.parents('div.source-media-panel-container').css('margin-top', '-1px');
			}
			/**
			 * Restore layout resize
			 */
			if ( ImageGrid.cookie.exists('rate_of_west') ){
				var fullWidth = ImageGrid.panels.panelFull.outerWidth();
				ImageGrid.UILayout.sizePane("west", ImageGrid.cookie.get('rate_of_west', 'int')*ImageGrid.panels.panelFull.outerWidth()/100);
			}
			/**
			 * Call calcaulator rate
			 */
			ImageGrid.calculatorRate();
			/**
			 * Auto-resize when window resize
			 */
			$(window).resize(function(){
				var fullWidth = $('#jsn-showlist-media-layout').width();
				if (ImageGrid.panels.panelFull.outerWidth() > 100) {
					ImageGrid.UILayout.sizePane("west", ImageGrid.resizeRate.west*ImageGrid.panels.panelFull.outerWidth()/100);
				} else {
					ImageGrid.UILayout.sizePane("west", ImageGrid.resizeRate.west*$('#tab-showlist-setting').width()/100);
				}
			});
		};
		/**
		*
		* Calculator rate size
		*
		* @return: Calculator rate of width
		*/
		ImageGrid.calculatorRate  = function(){
			var westWidth        = ImageGrid.panels.panelWest.innerWidth();
			var centerWirth      = ImageGrid.panels.panelCenter.innerWidth();
			var fullWidth        = ImageGrid.panels.panelFull.outerWidth();
			if (westWidth < 1200 && !ImageGrid.cookie.exists('rate_of_west')) {
				ImageGrid.resizeRate = {west: 70, center: 30};
			} else {
				ImageGrid.resizeRate = {west: westWidth*100/fullWidth, center: centerWirth*100/fullWidth};
			}
		};

		/**
		 * Content resize
		 */
		ImageGrid.contentResize   = function(){
			$(ImageGrid.classImagesSort).each(function(){
				if ($(this).parents('div.ui-layout-center').length){
					if ($(this).find('div.media-item').length){
						$(this).removeClass('jsn-section-empty');
					}else{
						$(this).addClass('jsn-section-empty');
					}
				}
				/*if ( $(this).children('div:last').attr('class') != 'clr'){
					$(this).children('.clr').remove().end().append('<div class="clr" />');
				}*/
			});

			if ( ImageGrid.contents.sourceimages.children('div.ui-sortable').hasClass('showlist') && ImageGrid.contents.sourceimages.find('div.media-item').length > 0){
				var tmpItem = ImageGrid.contents.sourceimages.find('div.media-item:first');
				var contaierWidth   = tmpItem.innerWidth();
				var thumbnailHeight = tmpItem.children('div.item-thumbnail').outerHeight();
				var thumbnailWidth  = tmpItem.children('div.item-thumbnail').outerWidth();
			}
		};

		/**
		 * Sync refreshing
		 */
		ImageGrid.syncRefreshing  = function(treeRoot){
			if ( treeRoot == undefined ){
				treeRoot = ImageGrid.jsnjTree.getContainer();
			}
			treeRoot.children('li').each(function(){
				var current = $(this);
				var isSelected = current.hasClass('catselected');
				if (isSelected)
				{
					current.children('input.sync').attr('checked', true);
				}
				if (current.has('ul').length)
				{
					ImageGrid.syncRefreshing(current.children('ul'));
				}
			});
		};
		/**
		 * Get source images and showlist images by sync mode
		 */
		ImageGrid.getImagesSync = function( syncName, typeAppend ){
			var countImages = 0;
			var countShowlistImages = 0;
			progressBarRandomNumber = 0;
			if(ImageGrid.options.sourceType=="folder"){
				$('.progress-bar-container').remove();
				progressBarRandomNumber = Math.floor(1000*Math.random());
				newProgressBarContainerId = 'progress_bar_conatainer_'+progressBarRandomNumber;
				newProgressBarId = 'progress_bar_'+progressBarRandomNumber;
				ImageGrid.sourcePanelHeader.append('<div id="'+newProgressBarContainerId+'" class="progress-bar-container"><div class="progress-bar"><div id="'+newProgressBarId+'" class="progress mini"><div class="bar" style="width: 0;"></div></div></div></div>');
			}
			$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=images&task=loadSourceImages',{
				showListID : ImageGrid.options.showListID,
				sourceType : ImageGrid.options.sourceType,
				sourceName : ImageGrid.options.sourceName,
				selectMode : ImageGrid.options.selectMode,
				offset	   : 0,
				progressBarRandomNumber	   : progressBarRandomNumber,
				pagination : ImageGrid.options.pagination,
				cateName   : syncName
			}).success( function(res){
				if (typeAppend == 'append' ){
					/**
					 * Append response to source videos
					 */
					ImageGrid.sourceImages.html(res);
					ImageGrid.sourceImages.append('<div id="show-more-items"><button id="show-more-items-btn" class="btn">Show more images</button><input id="cateNameInShowlist" type="hidden" value=""></div>');
					ImageGrid.sourceImages.find('div.media-item').removeClass('media-item').addClass('media-item-is-selected');
					/**
					 * Append response to showlist
					 */
					//ImageGrid.showlistImages.append(res);
					/**
					 * Move all images from source to showlist
					 */
					//ImageGrid.showlistImages.find('div.media-item-is-selected').removeClass('media-item-is-selected').addClass('media-item');
					/**
					 * Init events
					 */
					ImageGrid.initEvents();
					//Save showlist
					//ImageGrid.saveShowlist();
					// repadding for image
					//ImageGrid.repaddingImage();
				}else{
					/**
					 * Append response to source videos
					 */
					ImageGrid.sourceImages.html(res);
					ImageGrid.sourceImages.append('<div id="show-more-items"><button id="show-more-items-btn" class="btn">Show more images</button><input id="cateNameInShowlist" type="hidden" value=""></div>');
					/**
					 * Add all video to showlist
					 */
					res = '<div id="res-items">'+res+'</div>';
					$(res).children().each(function(){
						$('div[id="'+$(this).attr('id')+'"]', ImageGrid.sourceImages).removeClass('media-item-is-selected').addClass('media-item');
						//$('div[id="'+$(this).attr('id')+'"]', ImageGrid.showlistImages).remove();
					});
					/**
					 * Init events
					 */
					ImageGrid.initEvents();
				}
				if(syncName!=''){
					ImageGrid.contents.categories.find('a.jtree-selected').removeClass('jtree-selected');
					ImageGrid.contents.categories.find('li[id="'+syncName+'"]').children('a').addClass('jtree-selected');
				}
				$("#cateNameInShowlist").val(syncName);
				//Add image loading thumbnail
				//ImageGrid.imageLoading(ImageGrid.sourceImages.find('img[alt="video thumbnail"]'));
				//ImageGrid.imageLoading(ImageGrid.showlistImages.find('img[alt="video thumbnail"]'));
				ImageGrid.showlistImages.find('div.media-item').addClass('item-loaded');
				ImageGrid.sourceImages.find('div.media-item').addClass('item-loaded');
				countImages = $('.media-item, .media-item-is-selected', ImageGrid.sourceImages).length;
				ImageGrid.reindexImageSource(countImages);
				$('#show-more-items-btn').attr("disabled", false);
				$('#show-more-items-btn').html(JSNISLang.translate('SHOWLIST_IMAGE_LOAD_MORE_IMAGES'));
				if(countImages >= ImageGrid.imageTotal || ImageGrid.imageTotal==-1){
					$('#show-more-items').hide();
				}else{
					$('#show-more-items').show();
				}
				countShowlistImages = $('.media-item', ImageGrid.showlistImages).length;
				if (!countImages)
				{
					ImageGrid.sourceImages.addClass("jsn-section-empty");
				}
				else
				{
					ImageGrid.sourceImages.removeClass("jsn-section-empty");
				}
				if (!countShowlistImages)
				{
					ImageGrid.showlistImages.addClass('jsn-section-empty');
				}
				else
				{
					ImageGrid.showlistImages.removeClass('jsn-section-empty');
				}
				setTimeout(function(){
					showLoading({removeall:true});
				}, 500);
			});
		};
		/**
		 * Init events
		 */
		ImageGrid.initEvents      = function(){
			/**
			 * Init JSN jTree
			 */
			if ( ImageGrid.contents.categories.data('jsn_jtree_initialized') === undefined){
				if ( ImageGrid.options.selectMode == 'sync'){
					var jsnjTreeOptions = {
						syncmode : true
					};
				}else{
					var jsnjTreeOptions = {
						syncmode : false
					};
				}

				ImageGrid.jsnjTree = ImageGrid.contents.categories.jsnjtree(jsnjTreeOptions).bind('jsn_jtree.selectitem', function(e, obj){
					$.when(
						ImageGrid.imageTotal = -1,
						ImageGrid.clickTreeIcon = true,
						ImageGrid.reloadImageSource(obj.attr('id'))
					);
				}).bind("jsn_jtree.sync", function(e, obj){
					showLoading();
					if ( obj.attr('checked') == 'checked' ){
						/**
						 * Save sync checked
						 */
						$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=images&task=savesync', {
							showlist_id : ImageGrid.options.showListID,
							sourceType : ImageGrid.options.sourceType,
							sourceName : ImageGrid.options.sourceName,
							album_extid   : obj.parent().attr('id')
						}).success(function(res){
							ImageGrid.getImagesSync( obj.parent().attr('id'), 'append' );
							obj.parent().addClass('catselected');
							ImageGrid.showlistImages.html('<div class="showlist-sync-item-notice jsn-bglabel"><span class="jsn-icon64 jsn-icon-refresh"></span>'+JSNISLang.translate('SHOWLIST_NOTICE_IMAGES_ARE_SYNCED')+'</div>');
						});
					}else{

						$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=images&task=removesync', {
							showlist_id : ImageGrid.options.showListID,
							sourceType : ImageGrid.options.sourceType,
							sourceName : ImageGrid.options.sourceName,
							album_extid   : obj.parent().attr('id')
						}).success(function(res){
							ImageGrid.getImagesSync( obj.parent().attr('id'), 'remove' );
							obj.parent().removeClass('catselected');
							catselected = false;
							$('.jsn-jtree-children, .jsn-jtree-open').each(function(){
								if($(this).hasClass('catselected')){
									catselected = true;
									return true;
								}
							});
							if(!catselected){
								ImageGrid.showlistImages.html('<div class="showlist-sync-item-notice jsn-bglabel"><span class="jsn-icon64 jsn-icon-refresh"></span>'+JSNISLang.translate('SHOWLIST_NOTICE_IN_SYNC_MODE')+'</div>');
							}
						});
					}
				});
			}
			/**
			 * UILayout init
			 */
			ImageGrid.initLayout();
			/**
			 * Init multiple
			 */
			if ( ImageGrid.options.selectMode == 'sync' ){
				ImageGrid.multipleselect.destroy();
			}else{
				ImageGrid.multipleselect.init();
			}

			/**
			 * Index videos
			 */
			ImageGrid.indexImages();
			/**
			 * Init sortable
			 */
			ImageGrid.sortable();
			/**
			 * Active button
			 */
			ImageGrid.activeButtonsAction();
			/**
			 * Move video
			 */
			ImageGrid.sourceImages.find('button.move-to-showlist').unbind("click").click(function(){
				var _append;
				showLoading({removeall:false});
				ImageGrid.contents.categories.find('a.jtree-selected').parent().addClass('catselected');
				ImageGrid.moveVideoToShowlist( $(this).parents('div.media-item'),_append,1,1 );
				ImageGrid.showlistImages.removeClass('jsn-section-empty');
				//$('.media-item-multiple-select',ImageGrid.sourceImages).removeClass('media-item-multiple-select').removeClass('multiselectable-previous');
				///ImageGrid.moveImageToShowlistBtt.removeClass('active');

			});
			/**
			 * Show more image button
			 */
			ImageGrid.sourceImages.find('#show-more-items-btn').unbind("click").click(function(){
				var id = $("#cateNameInShowlist").val();
				ImageGrid.reloadImageSource(id);
			});
			/**
			 * Animate to change videos show type
			 */
			$('a', ImageGrid.sourcePanelHeader).unbind("click").click(function(){
				
				if ( $(this).hasClass('media-show-grid') && !$(this).children('i').hasClass('active') ){
					$(this).children('i').addClass('active');
					$(this).next().children('i').removeClass('active');
					ImageGrid.sourceImages.fadeOut(300, function(){
						$(this).removeClass('showlist');

						//ImageGrid.contents.sourceimages.find('div.media-item').removeAttr('style');
						//ImageGrid.contents.sourceimages.find('div.media-item-is-selected').removeAttr('style');

						$(this).addClass('showgrid').fadeIn(300, function(){
							//Set status to cookie store
							ImageGrid.cookie.set('jsn-is-cookie-view-mode-image-source', false);
							ImageGrid.contentResize();
						});
					});
				}else if($(this).hasClass('media-show-list') && !$(this).children('i').hasClass('active')){
					$(this).children('i').addClass('active');
					$(this).prev().children('i').removeClass('active');
					ImageGrid.sourceImages.fadeOut(300, function() {
					  $(this).removeClass('showgrid').addClass('showlist').fadeIn(300, function(){
					  		//Set status to cookie store
					  		ImageGrid.cookie.set('jsn-is-cookie-view-mode-image-source', true);
							ImageGrid.contentResize();
					  });
					});
				}
			});
			/**
			 * Animate to change showlist videos show types
			 */
			ImageGrid.bindClickToShowlistShowTypeButton();
			/**
			 * Button to change jsnjtree
			 */
			$('button', ImageGrid.treePanelHeader).unbind("click").click(function(){
				if(ImageGrid.applyButton.hasClass('disabledOnclick')){
					ImageGrid.disableSaveButton(false);
				}
				$('.progress-bar-container').remove();
				if ( $(this).hasClass('expand-all') ){
					ImageGrid.jsnjTree.expand_all();
				}else if ( $(this).hasClass('collapse-all') ){
					ImageGrid.jsnjTree.collapse_all();
				}else if( $(this).hasClass('sync') ){
					if ( $(this).hasClass('btn-success') ){
						//Uncheck selected category item
						ImageGrid.contents.categories.find('a.jtree-selected').removeClass('jtree-selected');
						$(this).removeClass('btn-success');
						$(this).html(JSNISLang.translate('SYNC_UPPERCASE') + ': ' + JSNISLang.translate('OFF_UPPERCASE') )
						//Show delete showlist image button
						ImageGrid.deleteImageShowlistBtt.show();
						//Show edit showlist image button
						ImageGrid.editImageShowlistBtt.show();
						//Show move source image button
						ImageGrid.moveImageToShowlistBtt.show();
						//Change mode to normal
						ImageGrid.setOption('selectMode', '');
						//Remove sync
						ImageGrid.jsnjTree.removeSync();
						//Add notice
						//ImageGrid.showlistImages.html('<div class="jsn-bglabel">'+JSNISLang.translate('SHOWLIST_NOTICE_DRAG_AND_DROP')+'</div>');
						// remove sync
						ImageGrid.removeSync();
						ImageGrid.showlistImages.html('<div class="jsn-bglabel showlist-drag-drop-item-notice"><span class="jsn-icon64 jsn-icon-pointer"></span>'+JSNISLang.translate('SHOWLIST_NOTICE_DRAG_AND_DROP')+'</div>');
						//Save showlist
						ImageGrid.saveShowlist();
						//Set empty source images
						ImageGrid.sourceImages.html('<div id="show-more-items"><button id="show-more-items-btn" class="btn">Show more images</button><input id="cateNameInShowlist" type="hidden" value=""></div>');
						$('#progress-bar-container').hide();
					}else{
						var syncButton = $(this);
						if (ImageGrid.showlistImages.find('div.media-item').length > 0){
							var confirmBox = confirm('When enabling "Sync mode", all current images in the showlist will be removed from it. Do you want to continue?');
							if (confirmBox == true)
							{
								$('li', ImageGrid.contents.categories).removeClass("catselected");
								//Uncheck selected category item
								ImageGrid.contents.categories.find('a.jtree-selected').removeClass('jtree-selected');
								//Change mode to normal
								ImageGrid.setOption('selectMode', 'sync');
								//Hide delete showlist video button
								ImageGrid.deleteImageShowlistBtt.hide();
								//Hide edit video showlist button
								ImageGrid.editImageShowlistBtt.hide();
								//Hide move source video button
								ImageGrid.moveImageToShowlistBtt.hide();
								//Set empty source images
								ImageGrid.sourceImages.html('<div id="show-more-items"><button id="show-more-items-btn" class="btn">Show more images</button><input id="cateNameInShowlist" type="hidden" value=""></div>');
								//Add sync button to active
								syncButton.addClass('btn-success');
								syncButton.html(JSNISLang.translate('SYNC_UPPERCASE') + ': ' + JSNISLang.translate('ON_UPPERCASE'))
								//Add notice
								ImageGrid.showlistImages.html('<div class="showlist-sync-item-notice jsn-bglabel"><span class="jsn-icon64 jsn-icon-refresh"></span>'+JSNISLang.translate('SHOWLIST_NOTICE_IN_SYNC_MODE')+'</div>');
								// Resize content
								ImageGrid.contentResize();
								//Save showlist
								ImageGrid.saveShowlist();
								//Add sync
								ImageGrid.jsnjTree.sync();
								// remove class catselected
								//imageGrid.removecatSelected();
								ImageGrid.contents.categories.find('ul li').each(function(){
									$(this).addClass('catsyn');
								});
							}
						}else{
							//Uncheck selected category item
							ImageGrid.contents.categories.find('a.jtree-selected').removeClass('jtree-selected');
							//Change mode to normal
							ImageGrid.setOption('selectMode', 'sync');
							//Hide delete showlist button
							ImageGrid.deleteImageShowlistBtt.hide();
							//Hide edit showlist button
							ImageGrid.editImageShowlistBtt.hide();
							//Hide move video from source video
							ImageGrid.moveImageToShowlistBtt.hide();
							//Set empty source images
							ImageGrid.sourceImages.html('<div id="show-more-items"><button id="show-more-items-btn" class="btn">Show more images</button><input id="cateNameInShowlist" type="hidden" value=""></div>');
							//Add sync button to active
							syncButton.addClass('btn-success');
							syncButton.html(JSNISLang.translate('SYNC_UPPERCASE') + ': ' + JSNISLang.translate('ON_UPPERCASE'))
							//Remove all video in the showlist
							ImageGrid.showlistImages.html('<div class="showlist-sync-item-notice jsn-bglabel"><span class="jsn-icon64 jsn-icon-refresh"></span>'+JSNISLang.translate('SHOWLIST_NOTICE_IN_SYNC_MODE')+'</div>');
							// Resize content
							ImageGrid.contentResize();
							//Save showlist
							ImageGrid.saveShowlist();
							//Add sync
							ImageGrid.jsnjTree.sync();
						}
					}
				}
			});

			//Restore source videos showtype
			if ( ImageGrid.cookie.exists('jsn-is-cookie-view-mode-image-source') && ImageGrid.cookie.get('jsn-is-cookie-view-mode-image-source') == 'true' ){
				ImageGrid.sourceImages.removeClass('showgrid').addClass('showlist');
			}
			ImageGrid.RestoreSourceImagesShowType();
			//Restore showlist videos showtype
			if ( ImageGrid.cookie.exists('jsn-is-cookie-view-mode-imageshow-showlist') && ImageGrid.cookie.get('jsn-is-cookie-view-mode-imageshow-showlist')  == 'true' ){
				ImageGrid.showlistImages.removeClass('showgrid').addClass('showlist');
			}
			ImageGrid.RestoreShowlistShowType();
			/**
			 * Resize content
			 */
			ImageGrid.contentResize();
		};
		/**
		 * Restore Showlist Show Type
		 */
		ImageGrid.RestoreShowlistShowType = function() {
			var countShowlistImages = $('.media-item', ImageGrid.showlistImages).length;
			if (!countShowlistImages)
			{
				ImageGrid.showlistPanelHeader.children('a.media-show-grid').unbind("click");
				ImageGrid.showlistPanelHeader.children('a.media-show-grid').children('i').removeClass('active').addClass('disabled');
				ImageGrid.showlistPanelHeader.children('a.media-show-list').unbind("click");
				ImageGrid.showlistPanelHeader.children('a.media-show-list').children('i').removeClass('active').addClass('disabled');
			}
			else
			{
				ImageGrid.showlistPanelHeader.children('a.media-show-grid').bind("click");
				ImageGrid.showlistPanelHeader.children('a.media-show-grid').children('i').removeClass('disabled');
				ImageGrid.showlistPanelHeader.children('a.media-show-list').bind("click");
				ImageGrid.showlistPanelHeader.children('a.media-show-list').children('i').removeClass('disabled');
				if (ImageGrid.cookie.exists('jsn-is-cookie-view-mode-imageshow-showlist') && ImageGrid.cookie.get('jsn-is-cookie-view-mode-imageshow-showlist')  == 'true'){
					ImageGrid.showlistPanelHeader.children('a.media-show-grid').children('i').removeClass('active');
					ImageGrid.showlistPanelHeader.children('a.media-show-list').children('i').addClass('active');
				}
				else
				{
					ImageGrid.showlistPanelHeader.children('a.media-show-grid').children('i').addClass('active');
					ImageGrid.showlistPanelHeader.children('a.media-show-list').children('i').removeClass('active');
				}
			}
		};
		/**
		 * Bind click even to showtype button of showlist
		 */
		ImageGrid.bindClickToShowlistShowTypeButton = function() {
			$('a', ImageGrid.showlistPanelHeader ).unbind("click").click(function(){
				if ( $(this).hasClass('media-show-grid') && !$(this).children('i').hasClass('active') ){
					$(this).children('i').addClass('active');
					$(this).next().children('i').removeClass('active');
					ImageGrid.showlistImages.fadeOut(300, function(){
						$(this).removeClass('showlist');
						ImageGrid.contents.showlistimages.find('div.media-item').removeAttr('style');
						$(this).addClass('showgrid').fadeIn(300, function(){
							//Set status to cookie store
							ImageGrid.cookie.set('jsn-is-cookie-view-mode-imageshow-showlist', false);
							ImageGrid.contentResize();
						});
					});
				}else if($(this).hasClass('media-show-list') && !$(this).children('i').hasClass('active')){
					$(this).children('i').addClass('active');
					$(this).prev().children('i').removeClass('active');
					ImageGrid.showlistImages.fadeOut(300, function(){
						$(this).removeClass('showgrid').addClass('showlist').delay(300).fadeIn(300, function(){
							//Set status to cookie store
							ImageGrid.cookie.set('jsn-is-cookie-view-mode-imageshow-showlist', true);
							ImageGrid.contentResize();
						});
					});
				}
			});
		};
		/**
		 * Restore Source Show Type
		 */
		ImageGrid.RestoreSourceImagesShowType = function() {
			var countSourceImages = $('.media-item, .media-item-is-selected', ImageGrid.sourceImages).length;
			if (!countSourceImages)
			{
				ImageGrid.sourcePanelHeader.children('a.media-show-grid').unbind("click");
				ImageGrid.sourcePanelHeader.children('a.media-show-grid').children('i').removeClass('active').addClass('disabled');
				ImageGrid.sourcePanelHeader.children('a.media-show-list').unbind("click");
				ImageGrid.sourcePanelHeader.children('a.media-show-list').children('i').removeClass('active').addClass('disabled');
				
			}
			else
			{
				ImageGrid.sourcePanelHeader.children('a.media-show-grid').bind("click");
				ImageGrid.sourcePanelHeader.children('a.media-show-grid').children('i').removeClass('disabled');
				
				ImageGrid.sourcePanelHeader.children('a.media-show-list').bind("click");
				ImageGrid.sourcePanelHeader.children('a.media-show-list').children('i').removeClass('disabled');
				
				if (ImageGrid.cookie.exists('jsn-is-cookie-view-mode-image-source') && ImageGrid.cookie.get('jsn-is-cookie-view-mode-image-source') == 'true' ){
					ImageGrid.sourcePanelHeader.children('a.media-show-grid').children('i').removeClass('active');
					ImageGrid.sourcePanelHeader.children('a.media-show-list').children('i').addClass('active');
				}
				else
				{
					ImageGrid.sourcePanelHeader.children('a.media-show-grid').children('i').addClass('active');
					ImageGrid.sourcePanelHeader.children('a.media-show-list').children('i').removeClass('active');
				}
			}
		};
		/**
		 * Active buttons action
		 */
		ImageGrid.activeButtonsAction = function(){
			/**
			 * Edit and Delete video showlist
			 */
			if ( ImageGrid.multipleselect.hasChildSelected(ImageGrid.showlistImages) ){
				ImageGrid.editImageShowlistBtt.children('i').removeClass('disabled');
				//ImageGrid.editImageShowlistBtt.parent().removeClass('disabled');
				ImageGrid.deleteImageShowlistBtt.children('i').removeClass('disabled');
				//ImageGrid.deleteImageShowlistBtt.parent().removeClass('disabled');
				ImageGrid.editImageShowlistBtt.unbind("click").click(function(){
					ImageGrid.editImage( $(ImageGrid.multipleselect.getAll(ImageGrid.showlistImages)) );
				});

				//ImageGrid.deleteVideoShowlistBtt.unbind("click").click(function(){
                ImageGrid.deleteImageShowlistBtt.unbind("click").click(function(){
                	var images = new Array(), z = 0;
					var videosMultipleSelected = ImageGrid.multipleselect.getAll(ImageGrid.showlistImages);
					var confirmBox = confirm('Are you sure you want to remove selected images?');
					if (confirmBox == true)
					{
						showLoading({removeall:false});
						ImageGrid.showlistImages.find('div.media-item-multiple-select').each(function(){
							var id = $(this).attr('id');
							images[z] = new Array();
							images[z] = $(this).find('input.item_id').val();
							$(this).fadeOut(300,function(){
								$(this).remove();
								$('div[id="'+id+'"]', ImageGrid.sourceImages).each(function(){
									$(this).removeClass('media-item-is-selected').addClass('media-item');
								});
								ImageGrid.indexImages();
								ImageGrid.contentResize();
								ImageGrid.multipleselect.init();
								ImageGrid.removeselectedAlbum();
								ImageGrid.RestoreShowlistShowType();
								ImageGrid.revertShowlistEmpty();
							});
							z++;
						});
						$.post(baseUrl + 'administrator/index.php?option=com_imageshow&controller=images&task=deleteimageshowlist',{
							showListID : ImageGrid.options.showListID,
							sourceName : ImageGrid.options.sourceName,
							sourceType : ImageGrid.options.sourceType,
							imageIDs   : ImageGrid.toJSON(images)
						}).success(function(responce){
							showLoading({removeall:true});
							ImageGrid.deleteImageShowlistBtt.unbind("click");
							ImageGrid.deleteImageShowlistBtt.children('i').addClass('disabled');
							ImageGrid.editImageShowlistBtt.children('i').addClass('disabled');
							ImageGrid.editImageShowlistBtt.unbind("click");								
						});
					}
				});

			}else{
				ImageGrid.editImageShowlistBtt.unbind('click');
				ImageGrid.editImageShowlistBtt.children('i').addClass('disabled');
				//ImageGrid.editImageShowlistBtt.parent().addClass('disabled');
				ImageGrid.deleteImageShowlistBtt.unbind('click');
				ImageGrid.deleteImageShowlistBtt.children('i').addClass('disabled');
				//ImageGrid.deleteImageShowlistBtt.parent().addClass('disabled');
			}

			/**
			 * Move selected video source
			 */
			if ( ImageGrid.multipleselect.hasChildSelected( ImageGrid.sourceImages ) ){
				ImageGrid.moveImageToShowlistBtt.children('i').removeClass('disabled');
				//ImageGrid.moveImageToShowlistBtt.parent().removeClass('disabled');
				ImageGrid.moveImageToShowlistBtt.unbind("click").click(function(){
					ImageGrid.contents.categories.find('a.jtree-selected').parent().addClass('catselected');
					showLoading({removeall:false});
					var i = 1;
					var _append;
					var videosMultipleSelected = ImageGrid.multipleselect.getAll(ImageGrid.sourceImages);
					var totalVideo =  videosMultipleSelected.length;
					ImageGrid.showlistImages.removeClass('jsn-section-empty');
					ImageGrid.queueExecute(videosMultipleSelected, 0, function(obj){

						ImageGrid.moveVideoToShowlist(obj, _append, totalVideo, i);

						if (i == totalVideo)
						{
							i = 0;
						}
						i++;
					});
				});
			}else{
				ImageGrid.moveImageToShowlistBtt.unbind('click');
				ImageGrid.moveImageToShowlistBtt.children('i').addClass('disabled');
				//ImageGrid.moveImageToShowlistBtt.parent().addClass('disabled');
			}
		};

		index = new Array();
		/**
		 * Multiple select
		 */
		ImageGrid.multipleselect  = {
			/**
			 * Init multiple select element
			 */
			init : function(){
				ImageGrid.multipleselect.multiselectable();
				/**
				 * Deselect all selected video from source
				 */
				ImageGrid.contents.sourceimages.unbind("click").click(function(e){

					if ( ImageGrid.multipleselect.hasChildSelected(ImageGrid.sourceImages) && !$(e.target).parents('div.media-item').length > 0 && !$(e.target).parents('div.media-item-is-selected').length > 0 ){
						$('#showlist-items div').each(function(){
							$(this).removeAttr('start');
							$('#start_item_showlist').val('');
							$('#stop_item_showlist').val('');
						});
						$('#source-items div').each(function(){
							$(this).removeAttr('start');
							$('#start').val('');
							$('#stop').val('');
						});
						ImageGrid.multipleselect.deSelectAll(ImageGrid.sourceImages);
						ImageGrid.activeButtonsAction();
					}
				});
				/**
				 * Deselecte all selected video from showlist
				 */
				ImageGrid.contents.showlistimages.unbind("click").click(function(e){
					if ( ImageGrid.multipleselect.hasChildSelected(ImageGrid.showlistImages) && !$(e.target).parents('div.media-item').length > 0 ){
						$('#showlist-items div').each(function(){
							$(this).removeAttr('start');
							$('#start_item_showlist').val('');
							$('#stop_item_showlist').val('');
						});
						$('#source-items div').each(function(){
							$(this).removeAttr('start');
							$('#start').val('');
							$('#stop').val('');
						});
						ImageGrid.multipleselect.deSelectAll(ImageGrid.showlistImages);
						ImageGrid.activeButtonsAction();
					}
				});
			},

			multiselectable: function()
			{
				ImageGrid.sourceImages.find('div.media-item').unbind("click").click(function(e) {
					var item = $(this),
						parent = item.parent(),
						myIndex = parent.children().index(item),
						prevIndex = parent.children().index(parent.find('.multiselectable-previous'));

					if(item.hasClass('media-item-multiple-select')){		//deselect item if it selected currently
						item.removeClass('media-item-multiple-select');
					}else{
						if (!e.ctrlKey && !e.metaKey)
						{
							parent.find('.media-item-multiple-select').removeClass('media-item-multiple-select')
						}
						else {
							if (item.not('.child').length) {
								if (item.hasClass('media-item-multiple-select'))
									item.nextUntil(':not(.child)').removeClass('media-item-multiple-select')
								else
									item.nextUntil(':not(.child)').addClass('media-item-multiple-select')
							}
						}

						if (e.shiftKey && prevIndex >= 0) {
							parent.find('.multiselectable-previous').toggleClass('media-item-multiple-select')
							if (prevIndex < myIndex)
								item.prevUntil('.multiselectable-previous').toggleClass('media-item-multiple-select')
							else if (prevIndex > myIndex)
								item.nextUntil('.multiselectable-previous').toggleClass('media-item-multiple-select')

							$('.media-item-is-selected', ImageGrid.sourceImages).removeClass('media-item-multiple-select');
						}

						item.toggleClass('media-item-multiple-select')
						parent.find('.multiselectable-previous').removeClass('multiselectable-previous')
						item.addClass('multiselectable-previous')
						ImageGrid.multipleselect.select($(this));
						ImageGrid.activeButtonsAction();
					}

				}).disableSelection()

				ImageGrid.showlistImages.find('div.media-item').unbind("click").click(function(e) {
					var item = $(this),
						parent = item.parent(),
						myIndex = parent.children().index(item),
						prevIndex = parent.children().index(parent.find('.multiselectable-previous'));

					if(item.hasClass('media-item-multiple-select')){			//deselect item if it selected currently
						item.removeClass('media-item-multiple-select');
					}else{
						if (!e.ctrlKey && !e.metaKey)
						{
							parent.find('.media-item-multiple-select').removeClass('media-item-multiple-select')
						}
						else {
							if (item.not('.child').length) {
								if (item.hasClass('media-item-multiple-select'))
									item.nextUntil(':not(.child)').removeClass('media-item-multiple-select')
								else
									item.nextUntil(':not(.child)').addClass('media-item-multiple-select')
							}
						}

						if (e.shiftKey && prevIndex >= 0) {
							parent.find('.multiselectable-previous').toggleClass('media-item-multiple-select')
							if (prevIndex < myIndex)
								item.prevUntil('.multiselectable-previous').toggleClass('media-item-multiple-select')
							else if (prevIndex > myIndex)
								item.nextUntil('.multiselectable-previous').toggleClass('media-item-multiple-select')
						}

						item.toggleClass('media-item-multiple-select')
						parent.find('.multiselectable-previous').removeClass('multiselectable-previous')
						item.addClass('multiselectable-previous')
						ImageGrid.multipleselect.select($(this));
						ImageGrid.activeButtonsAction();
					}
				}).disableSelection()
			},
			/**
			 * Destroy
			 */
			destroy : function(){
				ImageGrid.sourceImages.find('div.media-item').unbind("click");
				ImageGrid.showlistImages.find('div.media-item').unbind("click");
				ImageGrid.contents.sourceimages.unbind("click");
				ImageGrid.contents.showlistimages.unbind("click");
			},
			/**
			 * Get all elements was selected for multiple
			 */
			getAll : function(obj){
				return $(ImageGrid.classMultiple, obj);
			},
			/**
			 * Count multiple element
			 */
			getTotal : function(obj){
				return $(ImageGrid.classMultiple, obj).length;
			},
			/**
			 * Select element
			 */
			select : function(obj){
				obj.addClass(ImageGrid.classMultiple.replace('.', ''));
			},
			/**
			 * Deselect element
			 */
			deSelect : function(obj){
				obj.removeClass('.multiselectable-previous'.replace('.', ''));
				obj.removeClass(ImageGrid.classMultiple.replace('.', ''));
			},
			/**
			 * Deselect all elements
			 */
			deSelectAll : function(obj){
				ImageGrid.multipleselect.getAll(obj).removeClass('.multiselectable-previous'.replace('.', ''));
				ImageGrid.multipleselect.getAll(obj).removeClass(ImageGrid.classMultiple.replace('.', ''));
			},
			/**
			 * Check element multiple
			 */
			hasSelected : function(obj){
				return obj.hasClass(ImageGrid.classMultiple.replace('.', ''));
			},
			/**
			 * Check parent have child element are multiple
			 */
			hasChildSelected : function(obj){
				return ( $(ImageGrid.classMultiple, obj ).length > 0 ? true : false );
			}
		};
		/**
		*
		* Init function to set events and data
		*
		* @param: (array) (objs) is arrray elements
		* @param: (int) (i) is index item need init
		* @return: Init
		*/
		ImageGrid.sortable        = function(){
			ImageGrid.sourceImages.sortable({
				connectWith: 'div.showlist-items',
				items:'div.media-item',
				opacity: 0.6,
				scroll: true,
				dropOnEmpty: false,
				forceHelperSize: true,
				cancel: 'div.media-item-is-selected, div.item-no-found, div.showlist-drag-drop-item-notice, div.sync',
				scrollSensitivity: 50,
				helper: function(e, item){

					if ( ImageGrid.multipleselect.hasSelected( $(item) ) && ImageGrid.multipleselect.getTotal( ImageGrid.sourceImages ) > 1 ){
						var container = $('<div />', {
							'class' : 'jsn-media-item-multiple-select-container',
							'id'    : 'jsn-media-item-multiple-select-container'
						});
						var sumHeight = 0, i = 0;
						ImageGrid.multipleselect.getAll(ImageGrid.sourceImages).each(function(){
							sumHeight += $(this).height() + 9;
							var dragElement = $(this).clone(true);
							container.append( dragElement );
							$(this).data('i', i)
						});

						container.css({
							'height': sumHeight,
						});
					}else{
						container = $(item).clone(true);
					}
					$(item).show();

					return container;
				},
				start: function(event, ui) {
					var parent = ui.item.parent()
					if (parent.attr('id') == 'source-items')
					{
						var copy = $('.ui-sortable-placeholder').prev().clone(true);
						$('.ui-sortable-placeholder').after(copy);
						copy.show();
					}
					ImageGrid.receive = true;
				},
				update : function(event, ui){
					if ($(this).attr('id') == ImageGrid.showlistImages.attr('id'))
					{
						ImageGrid.activeButtonsAction();
						ImageGrid.contentResize();
					}
				},
				stop: function(event, ui){
					var parent = ui.item.parent();
				//	ImageGrid.saveShowlist();
					ImageGrid.showlistImages.find('div.item-no-found').remove();
					var elementID = ui.item.attr('id');
					if ( $('div[id="'+elementID+'"]', ImageGrid.sourceImages).length > 1){
						var index = 0;
						$('div[id="'+elementID+'"]', ImageGrid.sourceImages).each(function(){
							if (index > 0){
								$(this).remove();
							}
							index++;
						});
					}

					if ( $('div[id="'+elementID+'"]', ImageGrid.showlistImages).length > 1){
						var index = 0;
						$('div[id="'+elementID+'"]', ImageGrid.showlistImages).each(function(){
							if (index > 0){
								$(this).remove();
							}
							index++;
						});
					}
					ImageGrid.sourceImages.find('div.media-item-is-selected').each(function(){
						$(this).removeAttr('start');
					});
					ImageGrid.showlistImages.find('div.media-item').each(function(){
						$(this).removeAttr('start');
					});
					ImageGrid.indexImages();
					ImageGrid.multipleselect.init();
					ImageGrid.editImageShowlistBtt.children('i').addClass('disabled');
					//ImageGrid.editImageShowlistBtt.parent().addClass('disabled');
					ImageGrid.deleteImageShowlistBtt.children('i').addClass('disabled');
					//ImageGrid.deleteImageShowlistBtt.parent().addClass('disabled');
					if (parent.attr('id') != undefined && parent.attr('id') == 'showlist-items')
					{
						ImageGrid.contents.categories.find('a.jtree-selected').parent().addClass('catselected');
					}
					if ((parent.attr('id') != undefined && parent.attr('id') == 'showlist-items') && (!ImageGrid.receive))
					{
						ImageGrid.saveShowlist();
						setTimeout('showLoading({removeall:true})',1000);
					}

					ImageGrid.receive = false;
				}
			}).disableSelection();

			ImageGrid.showlistImages.sortable({
				opacity: 0.6,
				scroll: true,
				dropOnEmpty: false,
				forceHelperSize: true,
				cancel: 'div.media-item-is-selected, div.item-no-found, div.showlist-drag-drop-item-notice, div.sync',
				scrollSensitivity: 50,
				helper: function(e, item){
					if ( ImageGrid.multipleselect.hasSelected( $(item) ) && ImageGrid.multipleselect.getTotal( ImageGrid.showlistImages ) > 1 ){
						var container = $('<div />', {
							'class' : 'jsn-media-item-multiple-select-container',
							'id'    : 'jsn-media-item-multiple-select-container'
						});
						var sumHeight = 0, i = 0;
						ImageGrid.multipleselect.getAll(ImageGrid.showlistImages).each(function(){
							sumHeight += $(this).height() + 9;
							var dragElement = $(this).clone(true);
							$(this).hide();
							container.append( dragElement );
							$(this).data('i', i);
							i++;
						});

						container.css({
							'height': sumHeight
						});
					}else{
						container = $(item).clone(true);
					}
					$(item).show();

					return container;
				},
				receive: function(event, ui){
					showLoading({removeall:false});
					ImageGrid.receive = true;
				},

				update : function(event, ui){
					var elementID = ui.item.attr('id');
					if(ImageGrid.receive){
						var isMultipleVideo = ImageGrid.multipleselect.getTotal(ImageGrid.sourceImages);

						//showLoading({removeall:false});
						$('div[id="'+elementID+'"]', ImageGrid.sourceImages).removeClass('media-item').addClass('media-item-is-selected');
						if (isMultipleVideo)
						{
							var i = 1;
							ui.item.children('div.move-to-showlist').remove();
							ImageGrid.multipleselect.deSelect( $('div[id="'+elementID+'"]', ImageGrid.sourceImages));
							var totalVideoMoving = ImageGrid.multipleselect.getAll(ImageGrid.sourceImages);

							var _append = function(obj){
								ui.item.before(obj);
							};
							var totalVideo =  totalVideoMoving.length;
							ImageGrid.queueExecute(totalVideoMoving, 0, function(obj){
								ImageGrid.moveVideoToShowlist(obj, _append, totalVideo, i);
								_append = function(obj){
									ui.item.before(obj);
								};
								if (i == totalVideo)
								{
									i = 0;
								}
								i++;
							});
							if (totalVideoMoving.length == 0)
							{
								ImageGrid.saveOneImage();
							}
						}else{
							ImageGrid.saveOneImage();
						}


					}else{
						var isMultipleVideo = ImageGrid.multipleselect.hasSelected(ui.item);
						showLoading({removeall:false});

						ui.item.children('div.move-to-showlist').remove();
						//ImageGrid.showlistImages.children('div.clr').remove();
						var totalVideoMoving = ImageGrid.multipleselect.getAll(ImageGrid.showlistImages);
						$('#showlist-items .media-item-multiple-select').removeClass('media-item-multiple-select');
						var _append = function(obj){
							ui.item.before(obj);
						};

						totalVideoMoving.each( function (){
							if(ui.item.attr('id') != $(this).attr('id')){
								$(this).removeAttr('style');
								_append($(this));
							}
						});

						ImageGrid.saveShowlist();
						setTimeout('showLoading({removeall:true})',1000);
					}

					ImageGrid.indexImages();
					ImageGrid.contentResize();
					ImageGrid.showlistImages.removeClass('jsn-section-empty');
					$('.media-item-is-selected',ImageGrid.showlistImages).remove();
					//ImageGrid.removeselectedAlbum();

				},
				stop: function (){
					$('.media-item-multiple-select',ImageGrid.showlistImages).removeAttr('style');
					ImageGrid.receive = false;
				},
				over: function (){
					$('.showlist-drag-drop-item-notice').clone().appendTo('body').hide();
					$('.showlist-drag-drop-item-notice',ImageGrid.showlistImages).remove();
					ImageGrid.showlistImages.removeClass('jsn-section-empty');
				},
				out: function (){
					var countShowlistImages = $('.media-item', ImageGrid.showlistImages).length;					
					if(countShowlistImages <= 1){
						//ImageGrid.showlistImages.addClass('jsn-section-empty');
						$('.showlist-drag-drop-item-notice').appendTo(ImageGrid.showlistImages).show();	
					}
				}

			}).disableSelection();
		};

		/**
		 * Index video items
		 */
		ImageGrid.indexImages     = function(){
//			//Index source
//			var totalVideos = $('.media-item-is-selected', ImageGrid.sourceImages).length + $('.media-item', ImageGrid.sourceImages).length;
//			i = 1;
//			ImageGrid.sourceImages.children('div.media-item').each(function(){
//
//				if ( $(this).hasClass('media-item-is-selected') || $(this).hasClass('media-item') ){
//					$(this).children('div.item-index').html( i++ + '/' + totalVideos );
//					if (ImageGrid.options.selectMode != 'sync'){
//						var moveVideoToShowlist = $('<button />',{
//							'class' : "move-to-showlist"
//						}).html('&nbsp;');
//						$(this).children('div.item-index').append(moveVideoToShowlist);
//						moveVideoToShowlist.unbind("click").click(function(){
//							showLoading({removeall:false});
//							ImageGrid.contents.categories.find('a.jtree-selected').parent().addClass('catselected');
//							ImageGrid.moveVideoToShowlist( $(this).parents('div.media-item') );
//						});
//					}
//				}
//			});

			var totalVideos = $('.media-item', ImageGrid.showlistImages).length;
							  i = 1;
			ImageGrid.showlistImages.children().each(function(){
				if ( $(this).hasClass('media-item') ){
					$(this).children('div.item-index').html( i++ + '/' + totalVideos );
					if (ImageGrid.options.selectMode != 'sync'){
						$(this).children('div.item-index').append('<button class="delete-item pull-right icon-trash"></button><button class="edit-item pull-right icon-pencil"></button>');
						$(this).children('div.item-index').children('button.delete-item').click(function(){
							var video = $(this).parents('div.media-item');
							var confirmBox = confirm('Are you sure you want to remove selected images?');
							if (confirmBox == true)
							{
								ImageGrid.deleteImage(video);
								//jsnConfirm.dialog('close');
								showLoading({removeall:false});
							}
						});
						$(this).children('div.item-index').children('button.edit-item').click(function(){
							ImageGrid.editImage($(this).parents('div.media-item'));
						});
					}
				}
			});

			if ( ImageGrid.showlistImages.find('div.media-item').length == 0 ){
				if (ImageGrid.showlistImages.find('div.showlist-drag-drop-item-notice').length == 0 ){
					// remove duplicate noice in sync mode
					//ImageGrid.showlistImages.append('<div class="jsn-bglabel">'+JSNISLang.translate('SHOWLIST_NOTICE_DRAG_AND_DROP')+'</div>');
				}
			}else{
				ImageGrid.showlistImages.find('div.showlist-drag-drop-item-notice').remove();
			}
			ImageGrid.sourceImages.children('div.media-item').each(function(){
				$(this).dblclick(function(){
					// get current image click
					$(this).find('div.item-thumbnail img').each(function(){
						imgsrc  = $(this).attr('src');
					});
					//show popup with image detail
					$("#dialogboxdetailimage").html('<div class="img-box" style="background-color:#F4F4F4;width:440px;height:400px;display:table-cell; vertical-align:middle;"><img style="max-width: 400px; max-height: 360px;margin: 5px;" src="'+imgsrc+'"></div>')
					.dialog({
								width: 460,
								height: 620,
								modal: true,
								title: '<span style="font-size: 15px; font-weight:bold;">View Image</span>',
								buttons: [
								    {
								        text: "Close",
								        click: function() { $(this).dialog("close"); }
								    }
								]
							});
				});
			});

			//show popup edit image when double click to image

			ImageGrid.showlistImages.children('div.media-item').each(function(){
				$(this).unbind("dblclick").dblclick(function(){
					if ( ImageGrid.options.selectMode != 'sync'){
						ImageGrid.editImage($(this));
					}
				});
			});

		};
		/**
		 * Queue execute
		 */
		ImageGrid.queueExecute = function(queueArr, n, _callFunc){
			if ( n == queueArr.length ){
				return;
			}else{
				$(queueArr[n]).unbind("ImageGrid.execute.completed").bind("ImageGrid.execute.completed", function(){
					ImageGrid.queueExecute(queueArr, n+1, _callFunc);
				});
				if ( $.isFunction( _callFunc ) ){
					_callFunc( $(queueArr[n]) );
				}
			}
		};
		/**
		 * Move an element
		 */
		ImageGrid.moveVideoToShowlist = function(obj, _callFunc, total, i){
			$('.showlist-drag-drop-item-notice').remove();
			//$('#showlist-items .media-item-multiple-select').removeClass('media-item-multiple-select');
			//Deselect item
			ImageGrid.multipleselect.deSelect(obj);
			//Disable move button
			ImageGrid.activeButtonsAction();
			//Copy an media-item and append to showlist
			var copy = obj.clone(true);
			copy.removeAttr('style');
			//ImageGrid.showlistImages.children('div.clr').remove();
			//ImageGrid.showlistImages.children('div.clr').remove();
			if ( $.isFunction(_callFunc) ){
				_callFunc(copy);
			}else{
				copy.appendTo(ImageGrid.showlistImages);
			}
			obj.removeClass('media-item').addClass('media-item-is-selected');

			//Save showlist
			if ( $.isFunction(_callFunc) ){
				ImageGrid.saveShowlist();
			}
			else
			{
				ImageGrid.saveShowlistMovedAll(obj, total, i);
			}

			if(i <= total){
				//Re-index
				ImageGrid.indexImages();
				//Resize layout
				ImageGrid.contentResize();
				//Scroll to bottom
				if ( $.isFunction(_callFunc) ){
					ImageGrid.contents.showlistimages.animate({
						scrollTop : ImageGrid.contents.showlistimages.prop('scrollHeight')
					}, 1500, function(){
						if (i == total)
						{
							showLoading({removeall:true});
						}else{
							obj.trigger("ImageGrid.execute.completed");
						}

					});
				}
			}
		};

		//moving images is show list
		ImageGrid.moveImageInShowlist = function(obj, _callFunc, total, i){
			//Deselect item
			ImageGrid.multipleselect.deSelect(obj);
			//Disable move button
			ImageGrid.activeButtonsAction();
			//Copy an media-item and append to showlist
			var copy = obj.clone(true);

			copy.removeAttr('style');
			//ImageGrid.showlistImages.children('div.clr').remove();
			if ( $.isFunction(_callFunc) ){
				_callFunc(copy);
			}else{
				copy.appendTo(ImageGrid.showlistImages);
			}
			//ImageGrid.showlistImages.append('<div class="clr"></div>');


			//Save showlist
			if ( $.isFunction(_callFunc) ){
				//ImageGrid.saveShowlist();
			}
			else
			{
//				if (ImageGrid.options.sourceName == 'folder')
//					//ImageGrid.saveShowlistMovedAll(obj, total, i);
//				else
//					//ImageGrid.saveShowlist();
			}

			if(i <= total){
				//Re-index
				ImageGrid.indexImages();
				//Resize layout
				ImageGrid.contentResize();
				//Scroll to bottom
				if ( $.isFunction(_callFunc) ){
					ImageGrid.contents.showlistimages.animate({
						scrollTop : ImageGrid.contents.showlistimages.prop('scrollHeight')
					}, 1500, function(){
						obj.trigger("ImageGrid.execute.completed");
					});
				}else{
					//if (ImageGrid.options.sourceName != 'folder')
					//{
					//	obj.trigger("ImageGrid.execute.completed");
						//showLoading({removeall:true});
					//}
				}
			}
		};
		/**
		 * Convert array to JSON data
		 */
		ImageGrid.toJSON          = function( arr ){
			var json = new Array();
			var i = 0;
			for(k in arr){
				if (typeof arr[k] != 'function'){
					if (typeof arr[k] == 'Array' || typeof arr[k] == 'object'){
						json[i] = '"'+k+'":'+ImageGrid.toJSON(arr[k]);
					}else{
						json[i] = '"'+k+'":"'+arr[k]+'"';
					}
					i++;
				}
			}
			return '{'+json.join(',')+'}';
		};
		/**
		 * Check session ajax-responce
		 */
		ImageGrid.checkResponse   = function(res){
			$('input[type="hidden"]', res).each(function(i){
				if ($(this).attr('name') == 'task' && $(this).val() == 'login'){
					window.location.reload(true);
				}
	        });
		};

		ImageGrid.editImage = function(vEl){
			var params ='&showListID='+ImageGrid.options.showListID+'&imageID=';
			if(vEl.length>1){
				vEl.each(function(){
					params += $(this).find('input.item_id').val()+"|";
				});
			}
			else
			{
				params += vEl.find('input.item_id').val();
			}
			
			params = params.replace(/\./gi, '_jsnisdot_');
			var rand		= Math.floor((Math.random()*100)+1); 
			var iframeID 	= 'iframe-image-details-modal-' + rand;
			var link = baseUrl + 'administrator/index.php?option=com_imageshow&controller=image&task=editimage&sourceName='+ImageGrid.options.sourceName+'&sourceType='+ImageGrid.options.sourceType+'&tmpl=component'+params;			
			var modalSLSCImageDetailsWindowModal = new $.JSNISUIWindow(link,{
				width: $(window).width()*0.6,
				height: $(window).height()*0.7,
				title: JSNISLang.translate('SHOWLIST_EDIT_IMAGE_HEADER'),
				scrollContent: true,
				frameID: iframeID,
				buttons:
				[{
					text:'Save',
					click: function () {
						var iframe = $("#" + iframeID);
						var form = iframe.contents().find('#jsn-is-link-image-form');
						
						var modal = $(this);
						$.ajax({
							url: 'index.php?option=com_imageshow&controller=image&task=apply&ajax=1',
							data: $(form).serialize(),
							type: 'post',
							complete: function(jqXHR) {
								var json = JSON.parse(jqXHR.responseText);
								if (json.result == 'success')
								{	
									window.location.reload(true);
								}
							}
						});
						
					}
				},
				{
					text: 'Cancel',
					click: function (){
						$(this).dialog('close');
					}
				}
				]
			});	
		}
		/**
		 * Delete video
		 */
		ImageGrid.deleteImage    = function(vEl){
			var images = new Array(), z = 0;
				images[z] = new Array();
				images[z] = vEl.find('input.item_id').val();
			$.post(baseUrl + 'administrator/index.php?option=com_imageshow&controller=images&task=deleteimageshowlist',{
				showListID : ImageGrid.options.showListID,
				sourceName : ImageGrid.options.sourceName,
				sourceType : ImageGrid.options.sourceType,
				imageIDs   : ImageGrid.toJSON(images)
			}).success( function( responce ){
				ImageGrid.checkResponse(responce);
				var vID = vEl.attr('id');
				//vEl.trigger("ImageGrid.execute.completed");
				vEl.fadeOut(300, function(){
					$(this).remove();
					$('div[id="'+vID+'"]', ImageGrid.sourceImages).each(function(){
						$(this).removeClass('media-item-is-selected').addClass('media-item');
					});
					ImageGrid.indexImages();
					ImageGrid.contentResize();
					ImageGrid.multipleselect.init();
					ImageGrid.RestoreShowlistShowType();
					ImageGrid.revertShowlistEmpty();
					//ImageGrid.removeselectedAlbum();
				});
				showLoading({removeall:true});
				ImageGrid.deleteImageShowlistBtt.unbind("click");
				ImageGrid.deleteImageShowlistBtt.children('i').addClass('disabled');
				ImageGrid.editImageShowlistBtt.children('i').addClass('disabled');
				ImageGrid.editImageShowlistBtt.unbind("click");		
			});
		};
		
		/**
		 * Revert showlist empty status
		 */
		ImageGrid.revertShowlistEmpty = function (){			
			var count = $('.media-item', ImageGrid.showlistImages).length;
			if(count == 0){				
				$('<div class="jsn-bglabel showlist-drag-drop-item-notice jsn-section-empty"><span class="jsn-icon64 jsn-icon-pointer"></span>'+JSNISLang.translate('SHOWLIST_NOTICE_DRAG_AND_DROP')+'</div>').appendTo(ImageGrid.showlistImages).show();
			}
		}
		/**
		 * Create Thumb for preview
		 */
		ImageGrid.createThumbForPreview = function(elementID, inputID ,folderName, imageName, imagePath){
			$.post(baseUrl + 'administrator/index.php?option=com_imageshow&controller=images&task=createthumbforpreview&rand='+Math.random(),{
				folderName: folderName,
				imageName: imageName,
				imagePath: imagePath
			}).success(function(response){
				var data = JSON.parse(response);
				$('#'+elementID, ImageGrid.sourceImages).attr('src', data.image_path);//.parent().removeClass('isloading');
				$('#'+inputID, ImageGrid.sourceImages).val(data.encode_image_path);
			});
		};
		/**
		 * Override onclick event on "save" and "save and close" button
		 */
		ImageGrid.overrideSaveEvent = function(){
			ImageGrid.applyButton.add(ImageGrid.saveButton).each(function(){
			    $(this).data('onclick', this.onclick);
			    this.onclick = function(event) {
			    	if($(this).hasClass('disabledOnclick')) { // HERE
			    		return false;
			    	}
			    	$(this).data('onclick').call(this, event);
			    }
			})
		}
		/**
		 * Disable and enable "apply" and "save" event
		 */
		ImageGrid.disableSaveButton = function(disable){
			if(disable){
				ImageGrid.applyButton.add(ImageGrid.saveButton).each(function(){
					$(this).attr({title : JSNISLang.translate('SHOWLIST_DISALBE_SAVE_BUTTON')});
					$(this).children('span').addClass('ui-state-disabled');
					$(this).addClass("disabledOnclick");
				});
			}else{
				ImageGrid.applyButton.add(ImageGrid.saveButton).each(function(){
					$(this).removeAttr("title");
					$(this).children('span').removeClass('ui-state-disabled');
					$(this).removeClass("disabledOnclick");
				});
			}
		}
		/**
		 * Progress bar when generate thumbnail
		 */
		ImageGrid.progressBar = function(offset,containerId,progressBarId){
			ImageGrid.generatedThumbnailNumber++;
			countImages = $('.media-item, .media-item-is-selected', ImageGrid.sourceImages).length-offset;
			var percent = (ImageGrid.generatedThumbnailNumber/countImages)*100+'%';
			$('#'+progressBarId+' .bar').css('width',percent);
			if(percent=='100%'){
				$('#'+containerId).hide();
				ImageGrid.disableSaveButton(false);
			}
		}
		/**
		 *
		 * Save your drag and drop modulesList
		 *
		 * @return: Save to the database
		 * if (not success){
		 *	undo drag
		 *}
		 */
		ImageGrid.saveOneImage    = function(){
			var images = new Array(), i = 0;
			ImageGrid.showlistImages.find('div.media-item').each(function(){
				images[i] = new Array();
				images[i]['source_type']   	= ImageGrid.options.sourceType;
				images[i]['source_name']   	= ImageGrid.options.sourceName;
				images[i]['showlist_id']   	= ImageGrid.options.showListID;
				images[i]['imgid']   		= $(this).find('input.item_id').val();
				images[i]['order'] 			= $(this).index();
				images[i]['albumid'] 		= $(this).find('input.item_extid').val();
				images[i]['img_detail'] 	= JSON.parse($(this).find('input.item_detail').val());
				images[i]['img_thumb'] 		= $(this).find('input.img_thumb').val();
				i++;
			});
			$.post(baseUrl + 'administrator/index.php?option=com_imageshow&controller=images&task=saveshowlist',{
				showListID : ImageGrid.options.showListID,
				sourceName : ImageGrid.options.sourceName,
				sourceType : ImageGrid.options.sourceType,
				syncMode   : ImageGrid.options.selectMode,
				images     : ImageGrid.toJSON(images)
			}).success( function( responce ){
				showLoading({removeall:true});

				ImageGrid.checkResponse(responce);
				ImageGrid.multipleselect.deSelectAll(ImageGrid.showlistImages);
				ImageGrid.moveImageToShowlistBtt.unbind("click");
				ImageGrid.moveImageToShowlistBtt.children('i').addClass('disabled');
				ImageGrid.RestoreShowlistShowType();
				ImageGrid.bindClickToShowlistShowTypeButton();
				//ImageGrid.moveImageToShowlistBtt.parent().addClass('disabled');
			});
		};

		//reload source images
		ImageGrid.reloadImageSource = function (cateName){
			progressBarRandomNumber = 0;
			if(ImageGrid.options.sourceType=="folder"){
				$('.progress-bar-container').remove();
				progressBarRandomNumber = Math.floor(1000*Math.random());
				newProgressBarContainerId = 'progress_bar_conatainer_'+progressBarRandomNumber;
				newProgressBarId = 'progress_bar_'+progressBarRandomNumber;
				ImageGrid.sourcePanelHeader.append('<div id="'+newProgressBarContainerId+'" class="progress-bar-container"><div class="progress-bar"><div id="'+newProgressBarId+'" class="progress mini"><div class="bar" style="width: 0;"></div></div></div></div>');
			}
			if(cateName!=""){
				ImageGrid.contents.categories.find('a.jtree-selected').removeClass('jtree-selected');
				ImageGrid.contents.categories.find('li[id="'+cateName+'"]').children('a').addClass('jtree-selected');
			}	
			var countImages = 0;
			showLoading({height:ImageGrid.options.layoutHeight,removeall:false,element:ImageGrid.panels.panelFull});
			if(ImageGrid.options.pagination){
				if(!ImageGrid.clickTreeIcon){
					countImages = $('.media-item, .media-item-is-selected', ImageGrid.sourceImages).length;
				}
				$('#show-more-items-btn').attr("disabled", true);
				$('#show-more-items-btn').html('...'+JSNISLang.translate('SHOWLIST_IMAGE_LOADING'));
				$('#cateNameInShowlist').val(cateName);
			}
			$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=images&task=loadSourceImages', {
				showListID : ImageGrid.options.showListID,
				sourceType : ImageGrid.options.sourceType,
				sourceName : ImageGrid.options.sourceName,
				selectMode : ImageGrid.options.selectMode,
				pagination : ImageGrid.options.pagination,
				offset	   : countImages,
				progressBarRandomNumber	   : progressBarRandomNumber,
				cateName   : cateName
			}).success( function(res){
				if(ImageGrid.options.pagination){
					if(ImageGrid.clickTreeIcon){
						$('.media-item, .media-item-is-selected, .item-no-found', ImageGrid.sourceImages).remove();
						ImageGrid.clickTreeIcon = false;
					}
					$('#show-more-items').before(res);
				}else{
					ImageGrid.sourceImages.html(res);
				}
				//Add image thumbnail
				//ImageGrid.imageLoading(ImageGrid.sourceImages.find('img[alt="video thumbnail"]'));
				/**
				 * Init events
				 */
				ImageGrid.initEvents();
				ImageGrid.sourceImages.find('div.media-item').addClass('item-loaded');
				countImages = $('.media-item, .media-item-is-selected', ImageGrid.sourceImages).length;
				ImageGrid.reindexImageSource(countImages);
				if(ImageGrid.options.pagination){
					$('#show-more-items-btn').attr("disabled", false);
					$('#show-more-items-btn').html(JSNISLang.translate('SHOWLIST_IMAGE_LOAD_MORE_IMAGES'));
					if(countImages >= ImageGrid.imageTotal || ImageGrid.imageTotal==-1){
						$('#show-more-items').hide();
					}else{
						$('#show-more-items').show();
					}
				}
				if (!countImages)
				{
					ImageGrid.sourceImages.addClass("jsn-section-empty");
				}
				else
				{
					ImageGrid.sourceImages.removeClass("jsn-section-empty");
				}
				ImageGrid.RestoreSourceImagesShowType();
				showLoading({removeall:true,element:ImageGrid.panels.panelFull});
			})
		};
		/**
		 * Reindex image source
		 */
		ImageGrid.reindexImageSource = function(countImages){
			var btn = '';
			if(ImageGrid.options.selectMode!="sync")
				btn = '<button class="move-to-showlist pull-right icon-ok">&nbsp;</button>';
			var i = 1;
			var totalImages = (ImageGrid.imageTotal==-1)?countImages:ImageGrid.imageTotal;
			ImageGrid.sourceImages.children().each(function(){
				$(this).children('div.item-index').html( i++ + '/' + totalImages + btn);
			});
			if(btn!=''){
				ImageGrid.sourceImages.find('button.move-to-showlist').unbind("click").click(function(){
					var _append;
					showLoading({removeall:false});
					ImageGrid.contents.categories.find('a.jtree-selected').parent().addClass('catselected');
					ImageGrid.moveVideoToShowlist( $(this).parents('div.media-item'),_append,1,1 );
					ImageGrid.showlistImages.removeClass('jsn-section-empty');
				});
			}
		};
		/**
		 *
		 * Save your drag and drop modulesList
		 *
		 * @return: Save to the database
		 * if (not success){
		 *	undo drag
		 *}
		 */
		ImageGrid.saveShowlist    = function(){
				var images = new Array(), i = 0;
				ImageGrid.showlistImages.find('div.media-item').each(function(){
					images[i] = new Array();
					images[i]['source_type']   	= ImageGrid.options.sourceType;
					images[i]['source_name']   	= ImageGrid.options.sourceName;
					images[i]['showlist_id']   	= ImageGrid.options.showListID;
					images[i]['imgid']   		= $(this).find('input.item_id').val();
					images[i]['order'] 			= $(this).index();
					images[i]['albumid'] 		= $(this).find('input.item_extid').val();
					images[i]['img_detail'] 	= JSON.parse($(this).find('input.item_detail').val());
					images[i]['img_thumb'] 		= $(this).find('input.img_thumb').val();
					i++;
				});
			
				$.ajax({
					  url: baseUrl + 'administrator/index.php?option=com_imageshow&controller=images&task=saveshowlist',
					  type: 'post',
					  data: {
							showListID : ImageGrid.options.showListID,
							sourceName : ImageGrid.options.sourceName,
							sourceType : ImageGrid.options.sourceType,
							syncMode   : ImageGrid.options.selectMode,
							images     : ImageGrid.toJSON(images)
						},
					  async: false
					}).done(function() { 
						ImageGrid.moveImageToShowlistBtt.unbind('click');
						ImageGrid.moveImageToShowlistBtt.children('i').addClass('disabled');
						ImageGrid.multipleselect.deSelectAll(ImageGrid.showlistImages);
						ImageGrid.multipleselect.deSelectAll(ImageGrid.sourceImages);
						ImageGrid.RestoreShowlistShowType();
						ImageGrid.RestoreSourceImagesShowType();
						ImageGrid.bindClickToShowlistShowTypeButton();
					});
		};

		ImageGrid.saveShowlistMovedAll    = function(obj, total, j){
			var images = new Array(), i = 0;
			ImageGrid.showlistImages.find('div.media-item').each(function(){
				images[i] = new Array();
				images[i]['source_type']   	= ImageGrid.options.sourceType;
				images[i]['source_name']   	= ImageGrid.options.sourceName;
				images[i]['showlist_id']   	= ImageGrid.options.showListID;
				images[i]['imgid']   		= $(this).find('input.item_id').val();
				images[i]['order'] 			= $(this).index();
				images[i]['albumid'] 		= $(this).find('input.item_extid').val();
				images[i]['img_detail'] 	= JSON.parse($(this).find('input.item_detail').val());
				images[i]['img_thumb'] 		= $(this).find('input.img_thumb').val();
				i++;
			});
		$.post(baseUrl + 'administrator/index.php?option=com_imageshow&controller=images&task=saveshowlist',{
			showListID : ImageGrid.options.showListID,
			sourceName : ImageGrid.options.sourceName,
			sourceType : ImageGrid.options.sourceType,
			syncMode   : ImageGrid.options.selectMode,
			images     : ImageGrid.toJSON(images)
		}).success( function( responce ){
			obj.trigger("ImageGrid.execute.completed");
			if (j == total)
			{
				showLoading({removeall:true});
			}
			ImageGrid.checkResponse(responce);
			ImageGrid.multipleselect.deSelectAll(ImageGrid.showlistImages);
			ImageGrid.moveImageToShowlistBtt.unbind("click");
			ImageGrid.moveImageToShowlistBtt.children('i').addClass('disabled');
			//ImageGrid.moveImageToShowlistBtt.parent().addClass('disabled');
			ImageGrid.RestoreShowlistShowType();
			ImageGrid.bindClickToShowlistShowTypeButton();
		});
	};

		ImageGrid.selectlinkBtt.click(function(){
			$('#dialogbox2').bPopup({
		            closeClass:'close2',
		            content:'iframe',
		            follow:[false, false],
		            loadUrl:baseUrl+'administrator/index.php?option=com_imageshow&controller=image&view=image&task=showlinkpopup&layout=showlinkpopup&tmpl=component'
	        	});
		});

		ImageGrid.removeSync = function(){
			$.post( baseUrl+'administrator/index.php?option=com_imageshow&controller=images&task=removeallSync', {
				showListID : ImageGrid.options.showListID,
				sourceType : ImageGrid.options.sourceType,
				sourceName : ImageGrid.options.sourceName,
				syncMode   : ImageGrid.options.selectMode
			}).success(function(res){
				$('li', ImageGrid.contents.categories).removeClass("catselected");
			});
		}

		// remove prototies selected of a album if that album haven't any image in showlist after delete.
		ImageGrid.removeselectedAlbum = function(){
			var id_showlistarr = new Array();
			ImageGrid.showlistImages.find('div.image_extid').each(function(e){
				var id = $(this).attr('id').replace('cat_','');
				id_showlistarr[e] = id;
			});
			ImageGrid.contents.categories.find('li.catselected').each(function(){
				if($.inArray($(this).attr('id'),id_showlistarr ) > -1){
					// doesn't work anything :).
				}else{
					// remove cat don't have any images on showlist images
					$(this).removeClass('catselected');
				}
			});
		}

		return ImageGrid;
	};
	showLoading     = function(ops){
		//Option and overwrite option. jQuery extend
		var _ops = $.extend
		(
			{
				left           : 0,
				top            : 0,
				width          : $(document).width(),
				height         : $(document).height(),
				zIndex         : $.topZIndex(),
				showImgLoading : true,
				removeall      : false,
				element		   : 'body'
			},
			ops
		);
		if ( _ops.removeall ){
			$(_ops.element).find('div.ui-widget-overlay').remove();
			return;
		}
		var widgetOverlay = $(_ops.element).children('div.ui-widget-overlay');
		if ( widgetOverlay.length > 0 ){
			return;
		}
		if ( widgetOverlay.length == 0 ){
		   	var widgetOverlay = $('<div />', {
				'class' : 'ui-widget-overlay'
           	}).css({
           		'top'    : _ops.top,
           		'left'   : _ops.left,
           		'width'  : _ops.width,
           		'height' : _ops.height,
           		'z-index': _ops.zIndex
           	}).appendTo($(_ops.element));
			//Add image loading
			if ( _ops.showImgLoading ){

				if ( widgetOverlay.find('.img-box-loading').length ){
					widgetOverlay.find('.img-box-loading').remove();
				}
				if(_ops.element == 'body'){
					var top = $(window).scrollTop() + $(window).height()/2-12+'px';
					var left = $(window).scrollLeft() + $(window).width()/2-12+'px';
				}else{
					var height = ($(document).height()!=_ops.height)?_ops.height:$(_ops.element).height();
					var top =  height/2-12+'px';
					var left = $(_ops.element).width()/2-12+'px'
				}
				var imgBoxLoading = $('<div />', {
					                   'class' : 'img-box-loading'
				                    })
				                    .appendTo(widgetOverlay)
	                                .css({
	                                	'position': 'relative',
	                                	'top'     : top,
	                                	'left'    : left
	                                });

				$('<img />', {
					'src' : baseUrl+'administrator/components/com_imageshow/assets/images/icons-24/ajax-loader.gif'
				})
				.appendTo(imgBoxLoading)
	            .css({
            		'position': 'relative',
            		'left'    : '12px',
            		'top'     : '12px'
	            });
			}
		}
	}
	/**
	 * Manager
	 */
	var Instances = new Array();
	$.JSNISImageGridGetInstaces = function(options){
		if (Instances['JSNISImageGrid'] != undefined ){
			Instances['JSNISImageGrid'].setOption(options);
		}else{
			Instances['JSNISImageGrid'] = new $.JSNISImageGrid(options);
			var obj = Instances['JSNISImageGrid'];
		}
		return Instances['JSNISImageGrid'];
	};
})(jQuery);
