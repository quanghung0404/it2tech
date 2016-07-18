/**
 * JSN JTree is a plugin to help build tree of categoies in video grid
 *
 *
 */
(function($){
	$.fn.jsnjtree = function( options ){
		if ( $(this).children('ul').length == 0 ){
			alert('JSN JTree not found HTML tree.');
			return;
		}else{
			$(this).addClass('jsn-jtree');
		}
		//Get plugin options
		this.options = $.extend(
			{
				duration : 100,
				collapse : function(){
					
				},
				expand   : function(){
					
				}
			}
			, $.fn.jsnjtree._default()
			, options
		);
		//Get tree
		this.root = $(this).children('ul');
		// Make up HTML function
		this.makeupTree = function( tree ){
			var $this = this;
			tree.children('li').each(function(){
				// If item has chilrens
				if ( $(this).children('ul').length ){
					//Copy tree-child to store
					var treeChild = $(this).children('ul').clone( true );
					
					//Remove all children tags
					$(this).children().each(function(){
						$(this).remove();
					});
					
					var text = $(this).text();
					
					// Make tag empty HTML, Text, ...
					$(this).html('');
					
					var treeIcon = $('<ins />', {
						'class' : 'tree-icon'
					}).appendTo( $(this) ).html('&nbsp;');
					
					// Add tree text
					var treeText = $('<a />', {
						text : text
					}).appendTo( $(this) );
					
					// Copy new tree icon
					treeIcon.clone(true).prependTo(treeText);
					
					//Current item is single-item
					$(this).addClass('jsn-jtree-close');
					
					treeChild.appendTo( $(this) );
					
					// Call makeup childrens HTMLs
					$this.makeupTree( treeChild );
				}
				// If item is single
				else{
					//Remove all children tags
					$(this).children().each(function(){
						$(this).remove();
					});
					
					var text = $(this).text();
					
					// Make tag empty HTML, Text, ...
					$(this).html('');
					
					var insTreeIcon = $('<ins />', {
						'class' : 'tree-icon'
					}).appendTo( $(this) ).html('&nbsp;');
					
					// Add tree text
					var treeText = $('<a />', {
						text : text
					}).appendTo( $(this) );
					
					// Copy new tree icon
					insTreeIcon.clone(true).prependTo(treeText);
					
					//Current item is single-item
					$(this).addClass('jsn-jtree-children');
				}
			});
		};
		// Call makeup tree HTMLs
		this.makeupTree(this.root);
		/**
		 * Add event selector items
		 */
		
		var $this = this;
		$('a', this.root).mousedown(function(e){
			// Left-click select item
			if ( e.which == 1){
				//Reset item selected
				$this.root.find('a.jtree-selected').removeClass('jtree-selected');
				//Add current to item-selected
				$(this).addClass('jtree-selected');
				//Add trigger select item
				$this.trigger("jsn_jtree.selectitem", [$(this).parent()] );
			}
		});
		// Collspan/Expand event
		$('ins.tree-icon', this.root).click(function(e){			
			if ( $(this).parent().hasClass('jsn-jtree-open') ){
				var parent = $(this).parent();
				parent.children('ul').animate(
				{
					"height"  : "toggle", 
					"opacity" : "toggle"
				}, $this.options.duration, function(){
					parent.removeClass('jsn-jtree-open').addClass('jsn-jtree-close');
				});
				if ( $.isFunction($this.options.collapse)){
					$this.options.collapse(e, parent );
				}
			}else if ( $(this).parent().hasClass('jsn-jtree-close') ){
				var parent = $(this).parent();
				parent.children('ul').animate(
				{
					"height"  : "toggle", 
					"opacity" : "toggle"
				}, $this.options.duration, function(){
					parent.removeClass('jsn-jtree-close').addClass('jsn-jtree-open');
				});
				if ( $.isFunction($this.options.expand)){
					$this.options.expand(e, parent );
				}
			}
		});
		/**
		 * Get container
		 */
		this.getContainer = function(){
			return this.root.children('li').children('ul');
		};
		/**
		 * Expand all
		 */
		this.expand_all = function(root){
			if (root == undefined){	root = this.root; }
			$('li.jsn-jtree-close', root).each(function(){
				var obj = $(this);
				if ( obj.children('ul').length > 0 ){
					if ( obj.children('ul').css('display') == 'none' ){
						$this.expand_all(obj.children("ul"), 0);
						obj.children("ul").animate(
						{
							"height"  : "toggle", 
							"opacity" : "toggle"
						}, $this.options.duration, function(){
							obj.removeClass('jsn-jtree-close').addClass('jsn-jtree-open');
							$(this).show();
						});
					}else{
						obj.removeClass('jsn-jtree-close').addClass('jsn-jtree-open');
					}
				} 
			});
		};
		/**
		 * Collapse all
		 */
		this.collapse_all = function(root){
			if ( root == undefined ){root = this.root;}
			$('li.jsn-jtree-open', root).each(function(){
				var obj   = $(this);
				if (obj.children('ul').length > 0){
					if ( obj.children('ul').css('display') != 'none' ){
						$this.collapse_all(obj.children("ul"), 0);
						obj.children("ul").animate({
							"height"  : "toggle", 
							"opacity" : "toggle"
						}, $this.options.duration, function(){
							obj.removeClass('jsn-jtree-open').addClass('jsn-jtree-close');
							$(this).hide();
						});
					}else{
						obj.removeClass('jsn-jtree-open').addClass('jsn-jtree-close');
					}
				}
			});
		};

		//this.collapse_all(this.root);
		/**
		 * Add sync to categories
		 */
		this.sync = function(treeRoot){
			this.data('jsn_jtree.syncmode', true);
			if (treeRoot == undefined){treeRoot = this.getContainer();}
			treeRoot.children('li').each(function(){
				var sync = $('<input />', {
					'type'  : 'checkbox',
					'class' : 'sync'
				});
				
				$(this).children('.tree-icon').after(sync);
				
				//Sync action
				sync.click(function(e){
					$this.trigger('jsn_jtree.sync', [$(this)]);
				});
				//Call sync to childrens items
				if ( $(this).children('ul').length > 0 ){
					$this.sync($(this).children('ul'));
				}
			});
		};
		/**
		 * Remove all sync 
		 */
		this.removeSync = function(){
			this.data('jsn_jtree.syncmode', false);
			this.root.find('input.sync').remove();
		};
		//If sync is active then make tree for sync
		if ( this.options.syncmode ){
			this.sync();
		}
		//Add trigger when completed load tree
		this.trigger("jsn_jtree.loaded", [this.root]);
		//Add variable to able jsnjtree loaded
		this.data('jsn_jtree_initialized', true);
		//return plugin
		return this;
	};
	/**
	 * Default options
	 */
	$.fn.jsnjtree._default = function(){
		return {
			syncmode : false,
			duration : 200,
			collapse : function(event, obj){
				
			},
			expand   : function(event, obj){
				
			}
		};
	};
})(jQuery);
