<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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
        "{closeButton}" : "[data-close-button]",
        "{deleteButton}": "[data-delete-button]",
        "{form}": "[data-form-delete-post]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },

        "{deleteButton} click": function() {

            <?php if (JFactory::getApplication()->isAdmin()) { ?>
                this.form().submit();
            <?php } ?>

            <?php if (JFactory::getApplication()->isSite()) { ?>
            this.deleteButton().addClass("disabled");
            this.closeButton().addClass("disabled");

            EasyBlog.ajax('site/views/composer/delete', {
            "ids": <?php echo $uid; ?>
            }).done(function(url){
                // unbind the window event so that it will not prompt user
                // to choose 'stay' or leave.
                $(window).off('beforeunload');

                // some Joomla editor has the saving prompt feature. lets try to disable it.
                $(window).unbind('beforeunload');

                // for tinymce - cheap hack
                window.onbeforeunload = function() {};

                EasyBlog.ComposerLauncher.redirect(url);
            });
            <?php } ?>
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYBLOG_DIALOG_COMPOSER_DELETE_POST'); ?></title>
    <content>
        <form data-form-delete-post method="post" action="<?php echo JRoute::_('index.php');?>">
    	   <p><?php echo JText::_('COM_EASYBLOG_DIALOG_COMPOSER_DELETE_POST_CONFIRMATION');?></p>
           <input type="hidden" name="ids" value="<?php echo $uid;?>" />
           <?php echo $this->html('form.hidden', 'return', base64_encode(EBR::_('index.php?option=com_easyblog&view=composer&tmpl=component', false))); ?>
           <?php echo $this->html('form.action', 'posts.delete'); ?>
        </form>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-delete-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYBLOG_DELETE_BUTTON'); ?></button>
    </buttons>
</dialog>
