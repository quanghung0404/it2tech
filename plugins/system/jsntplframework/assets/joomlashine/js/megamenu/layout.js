/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function($) {
	$.JSNMMLayoutCustomizer = function(params) {
		// Initialize parameters
		this.params = $.extend({}, params);

		// Initialize functionality
		//$(document).ready($.proxy(this.init, this));
	};

	$.JSNMMLayoutCustomizer.prototype = {

		init: function(_this) {

			var self			= this;
			this.wrapper 		= $('.jsn-mm-form-container.jsn-layout');
			this.columns 		= $(_this).find('.jsn-column-container');
			
			this.addcolumns 	= '.add-container';
			this.addelements = '.jsn-mm-more-element';
            this.resizecolumns = '.ui-resizable-e';
            this.deletebtn = '.jsn-mm-item-delete';
            this.moveItemEl = "[class^='jsn-move-']";
            this.resize = 1;
            this.effect = 'easeOutCubic';			
			// Initialize variables
            this.maxWidth = $('.jsn-mm-form-container.jsn-layout').width();
            
            $('.jsn-mm-form-container.jsn-layout').css('width', this.maxWidth + 'px');
            this.spacing = 12;
            
            this.addRow(self, self.wrapper);
			this.updateSpanWidthPBDL(this, this.wrapper, this.maxWidth);
			this.initResizable(-1);
			this.addColumn($("#tmpl-jsn_tpl_mm_column").html());
			this.removeItem();		
			this.moveItem();
			this.moveItemDisable(this.wrapper);
			this.resizeHandle(this);
			this.addElement();
			this.rebuildSortable();
		},
	       // Show Add Elements Box
        addElement:function(){
            var self = this;

            this.wrapper.on("click", this.addelements, function(e){

                e.preventDefault();
                // Load modal instead of show popover.
               // $.JSNMMHandleElement.showLoading();
                $(window).scrollTop(0);

                var modal = new $.JSNTplModal({
                     dialogClass: 'jsn-mm-dialog jsn-bootstrap',
                     jParent: window.parent.jQuery.noConflict(),
                     title: 'Select Elements',
                     loaded: function (obj, iframe) {
                         
                         if ( $('#jsn-mm-add-element-modal').length == 0 ) {
                             obj.container.attr('id', 'jsn-mm-add-element-modal');
                         }

                        // var jParent  = $(iframe).contents();
                         
                         var htmlBox = $("#jsn-mm-add-element", $('#megamenu-setting-container')).clone();
                         
                         $('.jsn-modal').html('<div id="jsn-mm-add-element" class="jsn-mm-add-element add-field-dialog jsn-bootstrap">' + htmlBox.html() + '</div>');
                         // Replace close button by WR own close link
                         var closeButton = $('<a type="button" class="close jsn-mm-popover-close">&times;</a>');                         
                         $('.ui-dialog-titlebar-close').after(closeButton);                         
                         $('.ui-dialog-titlebar-close').remove();

                         $(closeButton).on('click', function(e) {
                             e.preventDefault();
                             obj.close();
                             $.JSNMMHandleElement.removeModal();
                         });

                        // $.HandleCommon.setFilterFields('.jsn-mm-add-element:last');
                        // $.HandleCommon.setQuickSearchFields('.jsn-mm-add-element:last');

                         // Set the height for content container   
                         $('#jsn-mm-add-element .jsn-items-list').height(this.height - 160);
                         self.updateSpanWidthPBDL(self, self.wrapper, $(".jsn-mm-form-container.jsn-layout").width());
                         self.updateSpanWidthPBDL(self, self.wrapper, $(".jsn-mm-form-container.jsn-layout").width());

                     },
                     fadeIn:200,
                     scrollable: true,
                     width: $.JSNMMHandleElement.resetModalSize(0, 'w'),
                     height: $(window.parent).height()*0.8
                 });

                $(window).resize(function() {
                    self.reCalculateSize('.jsn-mm-dialog-dialog');
                });

                modal.show();
            })
        },		
        // Get element's dimension
        getBoxStyle:function(element){
            var style = {
                width:element.width(),
                height:element.height(),
                outerHeight:element.outerHeight(),
                outerWidth:element.outerWidth(),
                offset:element.offset(),
                margin:{
                    left:parseInt(element.css('margin-left')),
                    right:parseInt(element.css('margin-right')),
                    top:parseInt(element.css('margin-top')),
                    bottom:parseInt(element.css('margin-bottom'))
                },
                padding:{
                    left:parseInt(element.css('padding-left')),
                    right:parseInt(element.css('padding-right')),
                    top:parseInt(element.css('padding-top')),
                    bottom:parseInt(element.css('padding-bottom'))
                }
            };

            return style;
        },		
        // Wrap content of row
        wrapContentRow:function(a,b,direction){
            var self = this;
            if(a.is(':animated') || b.is(':animated')) return;
            var this_wrapper = self.wrapper;
            var stylea = self.getBoxStyle(a);
            var styleb = self.getBoxStyle(b);
            var time = 500, extra1 = 16, extra2 = 16, effect = self.effect;
            if(direction > 0){
                a.animate({top: '-'+(styleb.height + extra1)+'px'}, time, effect, function(){});
                b.animate({top: ''+(stylea.height + extra2)+'px'}, time, effect, function(){
                    a.css('top', '0px');
                    b.css('top', '0px');
                    a.insertBefore(b);
                    self.moveItemDisable(this_wrapper);
                });
            }
            else{
                a.animate({top: ''+(styleb.height + extra2)+'px'}, time, effect, function(){});
                b.animate({top: '-'+(stylea.height + extra1)+'px'}, time, effect, function(){
                    a.css('top', '0px');
                    b.css('top', '0px');
                    a.insertAfter(b);
                    self.moveItemDisable(this_wrapper);
                });
            }
        },		
        moveItem:function()
        {
            var self = this;
            this.wrapper.on("click", this.moveItemEl, function() {
                if(!$(this).hasClass("disabled"))
                {
                    var otherRow, direction;
                    var class_ = $(this).attr("class");
                    var parent = $(this).parents(".jsn-row-container");
                    var parent_idx = parent.index(".jsn-row-container");
                    if (class_.indexOf("jsn-move-up") >= 0)
                    {
                        otherRow = self.wrapper.find(".jsn-row-container").eq(parent_idx-1);
                        direction = 1;
                    }
                    else if(class_.indexOf("jsn-move-down") >= 0)
                    {
                        otherRow = self.wrapper.find(".jsn-row-container").eq(parent_idx+1);
                        direction = -1;
                    }
                    
                    if (otherRow.length == 0) return;
                    self.wrapContentRow(parent, otherRow, direction);
                    // Set trigger timeout to be sure it happens after animation
                    setTimeout(function () {
                        self.wrapper.trigger('jsn-megamenu-layout-changed', [parent]);
                    }, 1001);

                }
            });
        },		
	    // Remove Row/Column/Element Handle
        removeItem:function(){
            var self = this;
            var this_wrapper = this.wrapper;
            this.wrapper.on("click", this.deletebtn, function(){
                if($(this).hasClass('row')){
                    $.JSNMMHandleCommon.removeConfirmMsg($(this).parents(".jsn-row-container"), 'row');
                    self.wrapper.trigger('jsn-megamenu-layout-changed', [parentForm]);
                }
                else if($(this).hasClass('column')){
                    var totalWidth = this_wrapper.width();
                    var parentForm = $(this).parents(".jsn-row-container");
                    var countColumn = parentForm.find(".jsn-column-container").length;
                    countColumn -= 1;
                    if(countColumn == 0){
                        // Remove this row
                        $.JSNMMHandleCommon.removeConfirmMsg(parentForm, 'column', $(this).parents(".jsn-column-container"));
                        self.wrapper.trigger('jsn-megamenu-layout-changed', [parentForm]);
                        return true;
                    }
                    var span = parseInt(12 / countColumn);
                    var exclude_span = (12 % countColumn != 0)? span + (12 % countColumn) : span;

                    // Remove current column
                    if(!$.JSNMMHandleCommon.removeConfirmMsg($(this).parents(".jsn-column-container"), 'column', null, function(){
                        // Update span remain columns
                        parentForm.find(".jsn-column-container").each(function () {
                            $(this).attr('class', $(this).attr('class').replace(/span[0-9]{1,2}/g, 'span'+span));
                            $(this).html($(this).html().replace(/span[0-9]{1,2}/g, 'span'+span));
                        });

                        // Update span last column
                        parentForm.find(".jsn-column-container").last().html(parentForm.find(".jsn-column-container").last().html().replace(/span[0-9]{1,2}/g, 'span'+exclude_span));

                        // Update width for all columns
                        self.updateSpanWidth(countColumn, totalWidth, parentForm);

                        // Actiave resizable for columns
                        self.initResizable(countColumn);
                        self.rebuildSortable();
                        self.wrapper.trigger('jsn-megamenu-layout-changed', [parentForm]);
                    }))
                        return false;
                }
                self.updateSpanWidthPBDL(self, self.wrapper, $(".jsn-mm-form-container.jsn-layout").width());
            });
        },		
        // Add Column
        addColumn:function(column_html){
            var self = this;
            this.wrapper.on('click', this.addcolumns, function() {
                var parentForm = $(this).parents(".jsn-row-container");
                var countColumn = parentForm.find(".jsn-column-container").length;
                
                if (countColumn < 12) 
                {
                    countColumn += 1;
                    var span = parseInt(12 / countColumn);
                    var exclude_span = (12 % countColumn != 0)? span + (12 % countColumn) : span;

                    // Update span old columns
                    parentForm.find(".jsn-column-container").each(function () {
                        $(this).attr('class', $(this).attr('class').replace(/span[0-9]{1,2}/g, 'span'+span));
                        $(this).html($(this).html().replace(/span[0-9]{1,2}/g, 'span'+span));
                    });

                    // Update span new column
                    column_html = column_html.replace(/span[0-9]{1,2}/g, 'span'+exclude_span);

                    // Add new column
                    parentForm.find(".jsn-mm-row-content").append(column_html);

                    // Update width for all columns
                    self.updateSpanWidth(countColumn, self.maxWidth, parentForm);
                }

                // Actiave resizable for columns
                self.initResizable(countColumn);
                self.rebuildSortable();
                self.wrapper.trigger('jsn-mm-megamenu-layout-changed', [parentForm]);
            });
        },
        
		addRow: function(self, wrapper) {
			
            this.wrapper.on('click', '#jsn-mm-add-container', function(e, getChosenLayout) {
                e.preventDefault();
                self._addRow(wrapper, this, getChosenLayout);
            });
 
            this.wrapper.on('click', '.jsn-mm-layout-thumbs .thumb-wrapper', function(event) {
                $(this).parent().find('.active').removeClass('active');
                $(this).addClass('active');

                $('#jsn-mm-add-container').trigger('click', [true]);
            });
            
			this.wrapper.on('mouseover', '#jsn-mm-add-container', function(event) {
				if (! $('.jsn-mm-layout-thumbs').hasClass('open'))
				{
					if ($(window).width() < 990) 
					{
						$('.jsn-mm-layout-thumbs').height(100);
					}
					else
					{
						$('.jsn-mm-layout-thumbs').height(50);
					}
					
					$('.jsn-mm-layout-thumbs').addClass('open');
				}
			});
			

			$('#jsn-mm-form-design-content').mouseleave(function(e) {
				if ($('.jsn-mm-layout-thumbs').hasClass('open')) 
				{
					$('.jsn-mm-layout-thumbs').removeClass('open');
					$('.jsn-mm-layout-thumbs').height(0);
				}
			});
		},
		
		_addRow: function(this_wrapper, target, get_chosen_layout) {
			var self = this;
			
			if ($(".jsn-mm-form-container.jsn-layout").find('.jsn-row-container').last().is(':animated'))
			{
				return;
			}
			
			// Animation
			var row_html = $(jsn_mm_remove_placeholder(
					$('#tmpl-jsn_tpl_mm_row').html(), 'custom_style',
					'style="display:none"'));
			
			var full_row_html = row_html.find('.jsn-mm-row-content').html();
			var html = '';
			
			if (get_chosen_layout && $('.jsn-mm-layout-thumbs .active').length > 0) 
			{
				var columns = $('.jsn-mm-layout-thumbs .active').attr('data-columns');
				
				columns = columns.split(',');
				$.each(columns, function(i, v) {
					html += full_row_html.replace(/\bspan\d+\b/g, 'span' + v);
				});
			}
			
			if (html !== '')
			{	
				row_html.find('.jsn-mm-row-content').html(html);
			}

			$(target).before(row_html);

			var new_el = $(".jsn-mm-form-container.jsn-layout").find(
					'.jsn-row-container').last();
			var height_ = new_el.height();
			if (height_ > 162)
			{
				height_ = 162;
			}	
			
			new_el.css({
				'opacity' : 0,
				'height' : 0,
				'display' : 'inline-block'
			});
			new_el.addClass('overflow_hidden');
			new_el.show();
			new_el.animate({
				height : height_
			}, 300, self.effect, function() {
				$(this).animate({
					opacity : 1
				}, 300, self.effect, function() {
					new_el.removeClass('overflow_hidden');
					new_el.css('height', 'auto');
				});
			});

			//last_row.fadeIn(1000);

			// Update width for colum of this new row
			var parentForm = self.wrapper.find(".jsn-row-container").last();
			var countColumn = parentForm.find(".jsn-column-container").length;
			// Actiave resizable for columns
            self.initResizable(countColumn);
			self.updateSpanWidth(1, self.maxWidth, parentForm);
			// Enable/disable move icons
			self.moveItemDisable(this_wrapper);
			self.rebuildSortable();
			self.updateSpanWidthPBDL(self, self.wrapper, $(
					".jsn-mm-form-container.jsn-layout").width());

		},
        // Update span width of columns in each row
        updateSpanWidth:function(countColumn, totalWidth, parentForm){
            //12px is width of the resizeable div
            var seperateWidth = (countColumn - 1) * 12;
            var remainWidth = totalWidth - seperateWidth;

            parentForm.find(".jsn-column-container").each(function (i) {
                var selfSpan = $(this).find(".jsn-column-content").attr("data-column-class").replace('span','');
                if(i == parentForm.find(".jsn-column-container").length - 1)
                {
                    $(this).find('.jsn-column').css('width', Math.ceil(parseInt(selfSpan)*remainWidth/12) + 'px');
                }
                else
                {	
                	$(this).find('.jsn-column').css('width', Math.floor(parseInt(selfSpan)*remainWidth/12) + 'px');
                }
            });
        },
        // Disable Move Row Up, Down Icons
        moveItemDisable:function(this_wrapper){
            var self    = this;
            this_wrapper.find(this.moveItemEl).each(function(){
                var class_ = $(this).attr("class");
                var parent = $(this).parents(".jsn-row-container");
                var parent_idx = parent.index(".jsn-row-container");

                // Add "disabled" class
                if(class_.indexOf("jsn-move-up") >= 0){
                    if(parent_idx == 0)
                        $(this).addClass("disabled");
                    else
                        $(this).removeClass("disabled");
                }
                else if(class_.indexOf("jsn-move-down") >= 0){
                    if(parent_idx == this_wrapper.find(".jsn-row-container").length -1)
                        $(this).addClass("disabled");
                    else
                        $(this).removeClass("disabled");
                }
            });
        },
        // Update column width when window resize
        resizeHandle:function (self) {
            $(window).resize(function() {          	
                if($('body').children('.ui-dialog').length)
                {	
                    $('html, body').animate({scrollTop: $('body').children('.ui-dialog').first().offset().top - 60}, 'fast');
                }
                self.fnReset(self);
                var _rows   = $('.jsn-row-container', self.wrapper);
                self.wrapper.trigger('jsn-megamenu-column-size-changed', [_rows]);          	
            });
//            $("#wr_page_builder").resize(function() {
//                self.fnReset(self);
//            });
        }, 
        // Reset when resize window/megamenu
        fnReset:function(self, trigger){
        	// Do this , trigger);
            if((self.resize || trigger) && $("#jsn-mm-form-design-content").width()){
                // Do this to prevent columns drop
                $(".jsn-mm-form-container.jsn-layout").width($("#jsn-mm-form-design-content").width() + 'px');
                self.maxWidth = $(".jsn-mm-form-container.jsn-layout").width();

                // Re-calculate step width
                self.calStepWidth(0, 'reset');
                self.initResizable(-1, false);
                self.updateSpanWidthPBDL(self, self.wrapper, self.maxWidth);


            }
            // Sortable elements
            this.sortableElement();
        },        
        // Update sortable event for row and column layout
        rebuildSortable:function () {
            // Sortable for columns in row
            var self    = this;
            $(".jsn-mm-row-content").sortable({
                axis:'x',
                //   placeholder:'ui-state-highlight',
                start:$.proxy(function (event, ui) {
                	var clone = ui.item.children().clone();
                    ui.placeholder.append(clone.css('width', clone.width()/2));
                    $(ui.item).parents(".jsn-mm-row-content").find(".ui-resizable-handle").hide();
                }, this),
                handle:".jsn-handle-drag",
                stop:$.proxy(function (event, ui) {
                	
                    $(ui.item).parents(".jsn-mm-row-content").find(".ui-resizable-handle").show();
                    self.wrapper.trigger('jsn-mm-megamenu-layout-changed', [ui.item]);
                }, this)
            });
            $(".jsn-mm-row-content").disableSelection();

            // Sortable for columns
            this.sortableElement();
        }, 
        // Sortable Element
        sortableElement:function(){
            var self    = this;
            $(".jsn-element-container").sortable({
                connectWith: ".jsn-element-container",
                placeholder: "ui-state-highlight",
                handle: '.drag-element-icon',
                start: function(event, ui) {
                    // Store original data
                    ui.item.css('position', '');
                    ui.item.parent().find('.ui-state-highlight, .ui-sortable-placeholder').hide();
                    ui.item.css('position', 'absolute');
                    ui.item.css('width',  400);
                    ui.item.parent().find('.ui-state-highlight, .ui-sortable-placeholder').show();
                   // ui.item.width(ui.item.width() * 0.7);
                },
                activate: function(event, ui) {
                  //  console.log(ui);
                    // Store orignal data
                },
                deactivate: function(event, ui) {
                    // Clean-up
                },
                receive: function(event, ui) {

                },

                stop: function (e, ui){
                    self.wrapper.trigger('jsn-mm-megamenu-layout-changed', [ui.item]);
                }
            });
            $(".jsn-element-container").disableSelection();
        },
        // Update span width of columns in each row of MegaMenu at Page Load
        updateSpanWidthPBDL:function(self, this_wrapper, totalWidth){
            this_wrapper.find(".jsn-row-container").each(function(){
                var countColumn = $(this).find(".jsn-column-container").length;
                self.updateSpanWidth(countColumn, totalWidth, $(this));
            })
        }, 
        
        initResizable:function (countColumn, getStep) {
            var self = this;
            
            if(getStep == null || getStep)
                self.calStepWidth(countColumn);

            var step = self.step;

            var handleResize = $.proxy(function (event, ui) {

                var thisWidth = ui.element.width(),
                bothWidth = ui.element[0].__next[0].originalWidth + ui.originalSize.width,
                nextWidth = bothWidth - thisWidth;

                if (thisWidth < step) {
                    thisWidth = step;
                    nextWidth = bothWidth - thisWidth;

                    // Set min width to prevent column from collapse more
                    ui.element.resizable('option', 'minWidth', step);
                } else if (nextWidth < step) {
                    nextWidth = step;
                    thisWidth = bothWidth - nextWidth;

                    // Set max width to prevent column from expand more
                    ui.element.resizable('option', 'maxWidth', thisWidth);
                }
                var this_span = parseInt(thisWidth / step);
                var next_span = parseInt(nextWidth / step);
                thisWidth = parseInt(parseInt(this_span)*bothWidth/(this_span + next_span));
                nextWidth = parseInt(parseInt(next_span)*bothWidth/(this_span + next_span));

                // Snap column to grid
                ui.element.css('width', thisWidth + 'px');

                // Resize next sibling element as well
                ui.element[0].__next.css('width', nextWidth + 'px');

                
                // Show % width
                self.percentColumn($(ui.element),"add",step);
                var _row    = $(ui.element).parents('.jsn-row-container');
                self.wrapper.trigger('jsn-mm-megamenu-column-size-changed', [_row]);
            }, this);
            // Reset resizable column
          
            $(".jsn-column", this.wrapper).each($.proxy(function (i, e) {
                $(e).resizable({
                    handles:'e',
                    minWidth:step,
                    grid:[step, 0],
                    start:$.proxy(function (event, ui) {                  	
                        ui.element[0].__next = ui.element[0].__next || ui.element.parent().next().children();
                        ui.element[0].__next[0].originalWidth = ui.element[0].__next.width();
                        ui.element.resizable('option', 'maxWidth', '');

                        // Disable resize handle
                        self.resize = 0;
                    }, this),
                    resize:handleResize,
                    stop:$.proxy(function (event, ui) {

                        var oldValue = parseInt(ui.element.find(".jsn-column-content").attr("data-column-class").replace('span', '')),
                        // Round up, not parsetInt
    
                        newValue = Math.round(ui.element.width() / step),
                        nextOldValue = parseInt(ui.element[0].__next.find(".jsn-column-content").attr("data-column-class").replace('span', ''));
                        // Update field values
                        if (nextOldValue > 0 && newValue > 0) {
                            ui.element.find(".jsn-column-content").attr("data-column-class", 'span' + newValue);
                            ui.element[0].__next.find(".jsn-column-content").attr('data-column-class', 'span' + (nextOldValue - (newValue - oldValue)));
                            // Update visual classes
                            ui.element.attr('class', ui.element.attr('class').replace(/\bspan\d+\b/, 'span' + newValue));
                            ui.element[0].__next.attr('class', ui.element[0].__next.attr('class').replace(/\bspan\d+\b/, 'span' + (nextOldValue - (newValue - oldValue))));
                            ui.element.find("[name^='shortcode_content']").first().text(ui.element.find("[name^='shortcode_content']").first().text().replace(/span\d+/, 'span' + newValue));
                            ui.element[0].__next.find("[name^='shortcode_content']").first().text(ui.element[0].__next.find("[name^='shortcode_content']").first().text().replace(/span\d+/, 'span' + (nextOldValue - (newValue - oldValue))));
                            $(e).css({
                                "height":"auto"
                            });
                        }

                        // Enable resize handle
                        self.resize = 1;
                        /// self.updateSpanWidthPBDL(self, self.wrapper, $(".wr-mm-form-container").width());

                        self.percentColumn($(ui.element),"remove",step);
                    }, this)
                });
            }, this));

            // Remove duplicated resizable-handle div
            if(countColumn > 0){
                $(".jsn-column").each(function(){
                    if($(this).find('.ui-resizable-handle').length > 1)
                        $(this).find('.ui-resizable-handle').last().remove();
                })
            }
        },
        percentColumn:function (element, action,step) {
            var self = this;
            if (action == "add") {

                var this_parent = $(element).parents(".jsn-column-container");
                // Get current columnm & next column
                var cols = [this_parent.find('.jsn-column'), this_parent.next('.jsn-column-container').find('.jsn-column')];

                // Count total span of this column & next column
                var spans = 0;
                $.each(cols, function () {
                    spans += parseInt(self.getSpan(this));
                })

                // Show percent tooltip of this column & the next column
                var updated_spans = [];
                $.each(cols, function (i) {
                    var thisCol = this;
                    var round = (i == cols.length - 1) ? 1 : 0;
                    var thisSpan = parseInt($(this).width() / step) + round;
                    if(i > 0){
                        thisSpan = ((spans - updated_spans[i - 1]) < thisSpan) ? (spans - updated_spans[i - 1]) : thisSpan;
                    }
                    updated_spans[i] = thisSpan;
                    self.showPercentColumn(thisCol, thisSpan);
                });

                // Show percent tooltip of other columns
                $(element).parents(".jsn-row-container").find(".jsn-column").each(function(){
                    if(!$(this).find(".jsn-mm-layout-percent-column").length){
                        var thisCol = this;
                        var thisSpan = self.getSpan(this);
                        self.showPercentColumn(thisCol, thisSpan);
                    }
                })

            }
            if (action == "remove") {
                var container = $(element).parents(".jsn-row-container");
                $(container).find(".jsn-mm-layout-percent-column").remove();
            }
        }, 
        getSpan:function(this_){
            return $(this_).find('.jsn-column-content').first().attr('data-column-class').replace('span', '');
        },
        // Show percent tooltip when know span of this column
        showPercentColumn:function(thisCol, thisSpan) {
            var maxCol = 12;
            var percent = this.toFixed(thisSpan / maxCol * 100, 2).replace(".00", "") + "%";
            var thumbnail = $(thisCol).find(".thumbnail");
            $(thumbnail).css('position', 'relative');
            // $(thumbnail).find("percent-column").remove();
            if ($(thumbnail).find(".jsn-mm-layout-percent-column").length) {
                $(thumbnail).find(".jsn-mm-layout-percent-column .jsn-percent-inner").html(percent);
            } else {
                $(thumbnail).append(
                    $("<div/>", {"class":"jsn-percent-column jsn-mm-layout-percent-column"}).append(
                        $("<div/>", {"class":"jsn-percent-arrow"})
                    ).append(
                        $("<div/>", {"class":"jsn-percent-inner"}).append(percent)
                    )
                )
            }
            var widthThumbnail = $(thumbnail).width();
            var widthPercent = $(thumbnail).find(".jsn-mm-layout-percent-column").width();
            $(thumbnail).find(".jsn-mm-layout-percent-column").css({"left":parseInt((widthThumbnail + 10) / 2) - parseInt(widthPercent / 2) + "px"});
            $(thumbnail).find(".jsn-mm-layout-percent-column .jsn-percent-arrow").css({"left":parseInt(widthPercent / 2) - 4 + "px"});
        }, 
        
        toFixed:function(value, precision){
            var power = Math.pow(10, precision || 0);
            return String(Math.round(value * power) / power);
        },  
        
        // Calculate step width when resize column
        calStepWidth:function(countColumn, reset){
            var this_column = this.columns;
            
            if(reset != null)
            {
            	
                this_column = $(".jsn-mm-form-container.jsn-layout").find(".jsn-row-container").first().find('.jsn-column-container');
            }

            var formRowLength = (countColumn > 0) ? countColumn : this_column.length;
            this.step = parseInt((this.maxWidth - (this.spacing * (formRowLength -1))) / 12);

        }, 
        // re-calculate sizes for modal select elements
        reCalculateSize: function (box) {

            var width = $.JSNMMHandleElement.resetModalSize(0, 'w');
            var height = $(window.parent).height() * 0.8;
            $(box).width(width);
            $(box).height(height);
            $('.jsn-items-list', $(box)).height(height);
            $(box).css({
                           top :'50%',
                           left :'50%',
                           margin :'-'+ (height / 2) +'px 0 0 -'+ ( (width / 2) - 7) +'px',
                           'z-index': '100001'
                       });
        },        
	};
	

    // Separate become common functions to call directly.
    $.JSNMMHandleCommon = $.JSNMMHandleCommon || {};

    // Confirm message when delete item
    $.JSNMMHandleCommon.removeConfirmMsg = function(item, type, column_to_row, callback) {
        var self = this;
        var msg = "";
        var show_confirm = 1;
        switch(type) {
            case 'row':
                if (item.find('.jsn-column-content').find('.shortcode-container').length == 0)
                {	
                    show_confirm = 0;
                }
                msg = JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_WHOLE_ROW'];
                break;
            case 'column':
                var check_item = (column_to_row != null) ? column_to_row : item;
                if (check_item.find('.shortcode-container').length == 0)
                {
                    show_confirm = 0;
                }
                msg = JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_WHOLE_COLUMN'];
                break;
            default:
                msg = JSNTPLMegamenuLangs['JSN_TPLFW_MEGAMENU_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_ELEMENT'];
        }

        var confirm_ = show_confirm ? confirm(msg) : true;
        if (confirm_)
        {
            if(type == 'row')
            {
                item.animate({opacity:0},300,$.JSNMMLayoutCustomizer.prototype.effect,function(){
                    item.animate({height:0},300,$.JSNMMLayoutCustomizer.prototype.effect,function(){
                        item.remove();
                        $.JSNMMLayoutCustomizer.prototype.moveItemDisable($(".jsn-mm-form-container.jsn-layout"));
                    });
                });
            }
            else if(type == 'column')
            {
                item.animate({height:0},500,$.JSNMMLayoutCustomizer.prototype.effect,function() {
                    item.remove();
                    if (callback != null) callback();
                });
            }
            else
            {
                item.remove();
            }
            return true;
        }
        else
        {
            return false;
        }
    };

    // Add event for filter field after load select element popover
    $.JSNMMHandleCommon.setFilterFields = function (id_parent) {
        // Filter
       // $(id_parent + " select.jsn-filter-button").select2("destroy");

        var filter_select = $(id_parent + " select.jsn-filter-button");
        filter_select.select2({
            minimumResultsForSearch:-1
        });

        if($(id_parent + " .jsn-quicksearch-field").val() != ''){
            $(id_parent + " #reset-search-btn").trigger("click");
        }
        else
            JSNLayoutCustomizer.prototype.elementFilter(id_parent, filter_select.val(), 'data-sort');

        $(id_parent + " .jsn-quicksearch-field").focus();
    };
    
    $.JSNMMHandleCommon.setQuickSearchFields = function (id_parent) {
        $.fn.delayKeyup = function (callback, ms) {
            var timer = 0;
            var el = $(this);
            $(this).keyup(function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback(el)
                }, ms);
            });
            return $(this);
        };
        $(id_parent + ' .jsn-quicksearch-field').keydown(function (e) {
            if(e.which == 13)
                return false;
        });
        $(id_parent + ' .jsn-quicksearch-field').delayKeyup(function(el) {
            if($(el).val() != '')
                $(id_parent + " #reset-search-btn").show();
            else
                $(id_parent + " #reset-search-btn").hide();

            ///self.filterElement($(el).val(), 'value');
            JSNLayoutCustomizer.prototype.elementFilter(id_parent, $(el).val().toLowerCase());
        }, 500);
        $(id_parent + ' .jsn-filter-button').change(function() {
            ///self.filterElement($(this).val(), 'type');
            JSNLayoutCustomizer.prototype.elementFilter(id_parent, $(this).val(), 'data-sort');
        })
        $(id_parent + ' #reset-search-btn').click(function(){
            ///self.filterElement("all");
            JSNLayoutCustomizer.prototype.elementFilter(id_parent, '');
            $(this).hide();
            $(id_parent + " .jsn-quicksearch-field").val("");
        })
    };
	
})(jQuery);
