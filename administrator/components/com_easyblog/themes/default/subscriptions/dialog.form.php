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
    <width>500</width>
    <height>180</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{submitButton}" : "[data-submit-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function()
        {
            this.parent.close();
        },
        "{submitButton} click" : function()
        {
            this.form().submit();
        }
    }
    </bindings>
    <title>
        <?php echo JText::_('COM_EASYBLOG_ADD_NEW_SUBSCRIBER_DIALOG_TITLE');?>
    </title>
    <content>
        <p style="margin-bottom:40px;"><?php echo JText::_('COM_EASYBLOG_ADD_NEW_SUBSCRIBER_DIALOG_CONTENT');?></p>

        <form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal mt-20" data-form-response>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTIONS_FULLNAME'); ?></label>
                <div class="col-md-7">
                    <input class="form-control" type="text" id="name" name="name" size="45" value="" data-subscribe-name />
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label>
                <div class="col-md-7">
                    <input type="text" id="email" name="email" class="form-control" value="" data-subscribe-email />
                </div>
            </div>
            <?php echo $this->html('form.hidden', 'type', $type);?>
            <?php echo $this->html('form.action', 'subscriptions.create'); ?>
        </form>

    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_ADD_BUTTON'); ?></button>
    </buttons>
</dialog>
