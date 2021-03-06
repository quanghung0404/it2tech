<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
        "{folderName}": "[data-folder-name]",
        "{submitButton}" : "[data-submit-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYBLOG_DIALOG_MM_RENAME_FOLDER'); ?></title>
    <content>
        <p><?php echo JText::_('COM_EASYBLOG_DIALOG_MM_RENAME_FOLDER_DESC');?></p>

        <div class="mt-20">
            <input data-folder-name placeholder="<?php echo JText::_('COM_EASYBLOG_MM_ENTER_NEW_NAME_FOR_FOLDER');?>" class="form-control" value="<?php echo $current;?>" />
        </div>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CLOSE_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_RENAME_FOLDER_BUTTON'); ?></button>
    </buttons>
</dialog>
