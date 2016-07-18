<?php
/**
 * @version    $Id: view.html.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

$sourceType = JRequest::getVar('sourceType');
$baseurl 	= ($sourceType == 'external') ? '' : JURI::root();
?>
<script type="text/javascript">
	var JSNISLinkWindow;
	var imageLinkID = '';
	require(['jquery', 'jsn/libs/modal'], function ($, JSNModal) {
		$(function () {

			$('.select-link-edit').click(function() {
				imageLinkID = $(this).attr('name');
				var link = 'index.php?option=com_imageshow&controller=image&task=linkpopup&tmpl=component';
				JSNISLinkWindow = new JSNModal({
					url: link,
					width: $(window).width()*0.9,
					height: $(window).height()*0.85,
					scrollable: true,
					title: '<?php echo JText::_('SHOWLIST_POPUP_IMAGE_CHOOSE_LINK', true); ?>',
					buttons: {
						'<?php echo JText::_('JSN_IMAGESHOW_CANCEL', true); ?>': function (){
							JSNISLinkWindow.close();
						}
					}
				});
				JSNISLinkWindow.iframe.css('overflow-x', 'hidden');
				JSNISLinkWindow.show();
			})

			$('textarea.description', $('#jsn-is-link-image-form')).wysiwyg({
			    controls: {
			        strikeThrough : { visible : true },
			        underline     : { visible : true },

			        separator00 : { visible : false },

			        justifyLeft   : { visible : false },
			        justifyCenter : { visible : false },
			        justifyRight  : { visible : false },
			        justifyFull   : { visible : false },

			        separator01 : { visible : true },

			        indent  : { visible : false },
			        outdent : { visible : false },

			        separator02 : { visible : false },

			        subscript   : { visible : false },
			        superscript : { visible : false },

			        separator03 : { visible : false },

			        undo : { visible : true },
			        redo : { visible : true },

			        separator04 : { visible : false },

			        insertOrderedList    : { visible : false },
			        insertUnorderedList  : { visible : false },
			        insertHorizontalRule : { visible : false },

			        h4mozilla : { visible : false && $.browser.mozilla, className : 'h4', command : 'heading', arguments : ['h4'], tags : ['h4'], tooltip : "Header 4" },
			        h5mozilla : { visible : false && $.browser.mozilla, className : 'h5', command : 'heading', arguments : ['h5'], tags : ['h5'], tooltip : "Header 5" },
			        h6mozilla : { visible : false && $.browser.mozilla, className : 'h6', command : 'heading', arguments : ['h6'], tags : ['h6'], tooltip : "Header 6" },

			        h4 : { visible : false && !( $.browser.mozilla ), className : 'h4', command : 'formatBlock', arguments : ['<H4>'], tags : ['h4'], tooltip : "Header 4" },
			        h5 : { visible : false && !( $.browser.mozilla ), className : 'h5', command : 'formatBlock', arguments : ['<H5>'], tags : ['h5'], tooltip : "Header 5" },
			        h6 : { visible : false && !( $.browser.mozilla ), className : 'h6', command : 'formatBlock', arguments : ['<H6>'], tags : ['h6'], tooltip : "Header 6" },

			        separator05 : { separator : false },

		            createLink : {
		                visible : false,
		                exec    : function()
		                {
		                    var selection = $(this.editor).documentSelection();

		                    if ( selection.length > 0 )
		                    {
		                        if ( $.browser.msie )
		                        {
		                            this.focus();
		                            this.editorDoc.execCommand('createLink', true, null);
		                        }
		                        else
		                        {
		                            var szURL = prompt('URL', 'http://');

		                            if ( szURL && szURL.length > 0 )
		                            {
		                                this.editorDoc.execCommand('unlink', false, []);
		                                this.editorDoc.execCommand('createLink', false, szURL);
		                            }
		                        }
		                    }
		                    else if ( this.options.messages.nonSelection )
		                        alert(this.options.messages.nonSelection);
		                },

		                tags : ['a'],
		                tooltip : "Create link"
		            },

		            insertImage : {
		                visible : false,
		                exec    : function()
		                {
		                    if ( $.browser.msie )
		                    {
		                        this.focus();
		                        this.editorDoc.execCommand('insertImage', true, null);
		                    }
		                    else
		                    {
		                        var szURL = prompt('URL', 'http://');

		                        if ( szURL && szURL.length > 0 )
		                            this.editorDoc.execCommand('insertImage', false, szURL);
		                    }
		                },

		                tags : ['img'],
		                tooltip : "Insert image"
		            },

		            separator06 : { separator : false },

		            h1mozilla : { visible : false && $.browser.mozilla, className : 'h1', command : 'heading', arguments : ['h1'], tags : ['h1'], tooltip : "Header 1" },
		            h2mozilla : { visible : false && $.browser.mozilla, className : 'h2', command : 'heading', arguments : ['h2'], tags : ['h2'], tooltip : "Header 2" },
		            h3mozilla : { visible : false && $.browser.mozilla, className : 'h3', command : 'heading', arguments : ['h3'], tags : ['h3'], tooltip : "Header 3" },

		            h1 : { visible : false && !( $.browser.mozilla ), className : 'h1', command : 'formatBlock', arguments : ['<H1>'], tags : ['h1'], tooltip : "Header 1" },
		            h2 : { visible : false && !( $.browser.mozilla ), className : 'h2', command : 'formatBlock', arguments : ['<H2>'], tags : ['h2'], tooltip : "Header 2" },
		            h3 : { visible : false && !( $.browser.mozilla ), className : 'h3', command : 'formatBlock', arguments : ['<H3>'], tags : ['h3'], tooltip : "Header 3" },
			        separator07 : { visible : false },

			        cut   : { visible : false },
			        copy  : { visible : false },
			        paste : { visible : false }
			      }
			    });
		    $('.wysiwyg').css('width', '96%');
		});
	});

	function jsnGetMenuItems(id, title, object,link)
	{
		var id = '#item_link';
		if (imageLinkID != '')
		{
			 id = id + '_' + imageLinkID;
		}
		jQuery(id).val(link);
		JSNISLinkWindow.close();
	}

	function jsnGetArticle(id, title, catid, object,link)
	{
		var id = '#item_link';
		if (imageLinkID !='')
		{
			 id = id + '_' + imageLinkID;
		}
		jQuery(id).val(link);
		JSNISLinkWindow.close();
	}
</script>
<div id="edit-item-details" class="jsn-bootstrap">
	<form name="editForm" method="post" action="" id="jsn-is-link-image-form">
		<?php
		$countImage = count($this->image);
		if($countImage > 1)
		{
			?>
		<div class="jsn-section-striped">
		<?php
		for($i=0; $i < $countImage; $i++)
		{
			?>
			<div id="edit-item-details-multiple">
				<div class="jsn-item-details">
					<div class="control-group pull-left">
						<div class="thumbnail jsn-item-thumbnail">
							<img class="jsn-box-shadow-light"
								src="<?php echo $baseurl . $this->image[$i]->image_small;?>"
								name="image" />
						</div>
					</div>
					<div class="control-group">
						<input type="text" class="jsn-input-medium-fluid title"
							name="title[]" id="item-title"
							value="<?php echo htmlspecialchars($this->image[$i]->image_title);?>" />
						<input type="hidden" name="originalTitle[]"
							value="<?php echo htmlspecialchars($this->image[$i]->image_title);?>" />
					</div>
					<div class="control-group" style="padding-left: 147px;">
						<textarea rows="3" class="jsn-input-large-fluid description" id="item-description" name="description[]"><?php echo htmlspecialchars($this->image[$i]->image_description);?></textarea>
						<input type="hidden" name="originalDescription[]" value="<?php echo htmlspecialchars($this->image[$i]->image_description);?>" />
						<div class="input-append">
							<input type="text" class="link" id="item_link_<?php echo $this->image[$i]->image_id;?>" value="<?php echo $this->image[$i]->image_link;?>" name="image_link[]" />
							<input class="btn select-link-edit" type="button" name="<?php echo $this->image[$i]->image_id;?>" value="..." />
						</div>
					</div>
					<input type="hidden" name="originalLink[]"
						value="<?php echo $this->image[$i]->image_link;?>" /> <input
						type="hidden" name="imageID[]"
						value="<?php echo $this->image[$i]->image_id;?>" /> <input
						type="hidden" name="image_extid[]"
						value="<?php echo $this->image[$i]->image_extid;?>" />
					<div class="clearbreak"></div>
				</div>
			</div>
			<?php
		}
		?>
			<input type="hidden" name="numberOfImages"
				value="<?php echo count($this->image);?>" /> <input type="hidden"
				name="showlistID"
				value="<?php echo $this->image[0]->showlist_id ;?>" /> <input
				type="hidden" name="option" value="com_imageshow" /> <input
				type="hidden" name="controller" value="image" /> <input
				type="hidden" name="task" value="apply" />
		</div>
		<?php
		}
		else
		{
			?>
		<div id="edit-item-details-single">
			<div class="jsn-item-details">
				<div class="control-group">
					<div class="thumbnail jsn-item-thumbnail jsn-single-item-thumbnail">
						<img class="jsn-box-shadow-light" src="<?php echo $baseurl . $this->image->image_small;?>" name="image" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('SHOWLIST_EDIT_IMAGE_TITLE');?>
					</label>
					<div class="controls">
						<input type="text" class="jsn-input-xxlarge-fluid title" name="title" id="item-title" value="<?php echo htmlspecialchars($this->image->image_title);?>" />
						<input type="hidden" name="originalTitle" value="<?php echo htmlspecialchars($this->image->image_title);?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('SHOWLIST_EDIT_IMAGE_DESCRIPTION');?>
					</label>
					<div class="controls">
						<textarea class="jsn-input-xxlarge-fluid description" rows="5" id="item-description" name="description"><?php echo htmlspecialchars($this->image->image_description);?></textarea>
						<input type="hidden" name="originalDescription" value="<?php echo htmlspecialchars($this->image->image_description);?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('SHOWLIST_EDIT_IMAGE_LINK');?>
					</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" id="item_link" class="link" value="<?php echo $this->image->image_link;?>" name="link" />
							<input class="btn select-link-edit" type="button" name="" value="..." />
						</div>
						<input type="hidden" name="originalLink" value="<?php echo $this->image->image_link;?>" />
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="numberOfImages" value="1" />
		<input type="hidden" name="option" value="com_imageshow" />
		<input type="hidden" name="controller" value="image" />
		<input type="hidden" name="task" value="apply" />
		<input type="hidden" name="imageID" value="<?php echo $this->image->image_id;?>" />
		<input type="hidden" name="image_extid" value="<?php echo $this->image->image_extid;?>" />
		<input type="hidden" name="showlistID" value="<?php echo $this->image->showlist_id ;?>" />
		<?php }?>
	</form>
</div>
