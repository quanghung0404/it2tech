<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>450</width>
	<height>180</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",
		"{title}": "[data-template-title]",
		"{system}": "[data-template-system]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_DIALOG_TITLE'); ?></title>
	<content>
		<p class="mt-5">
			<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_DIALOG_CONTENT');?>
		</p>
        <div class="mt-20">
            <input class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_TITLE_PLACEHOLDER'); ?>" data-template-title/>
        </div>
        <div class="mt-20">
            <div class="eb-checkbox">
                <input type="checkbox" id="system" name="system" value="1" data-template-system />
            	<label for="system">
                    <?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_AS_GLOBAL'); ?>
            	</label>
            </div>
        </div>
	</content>
	<buttons>
		<button type="button" class="btn btn-default btn-sm" data-close-button><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?></button>
		<button type="button" class="btn btn-primary btn-sm" data-submit-button><?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_BUTTON');?></button>
	</buttons>
</dialog>
