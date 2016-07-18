<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>400</width>
	<height>120</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-cancel-button]",
		"{form}": "[data-approve-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_ACCEPT_BLOG_POST_DIALOG_TITLE');?></title>
	<content>
		<form data-approve-form action="<?php echo JRoute::_('index.php');?>" method="post">
			<p><?php echo JText::_('COM_EASYBLOG_ACCEPT_BLOG_POST_DIALOG_CONTENT'); ?></p>
			<input type="hidden" name="cid[]" value="<?php echo $id;?>" />

			<?php echo $this->html('form.action', 'pending.approve'); ?>
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
		<button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_APPROVE_BUTTON'); ?></button>
	</buttons>
</dialog>
