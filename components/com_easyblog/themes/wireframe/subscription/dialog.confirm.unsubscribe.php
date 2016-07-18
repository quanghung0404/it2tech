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
        "{closeButton}": "[data-close-button]",
        "{submitButton}": "[data-submit-button]",
        "{form}": "[data-eb-unsubscribe-form]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },
        "{submitButton} click": function() {
            this.form().submit();
        }
    }
    </bindings>
    <title>
        <?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBCRIBE_CONFIRMATION');?>
    </title>
    <content>
        <form name="unsubscribe" method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-unsubscribe-form>
            <p><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBCRIBE_CONFIRMATION_DESC');?></p>

            <input type="hidden" name="id" value="<?php echo $subscription->id;?>" />
            <input type="hidden" name="option" value="com_easyblog" />
            <?php echo $this->html('form.token'); ?>
            <input type="hidden" name="task" value="subscription.unsubscribe" />
            <input type="hidden" name="return" value="<?php echo $return;?>" />
        </form>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CLOSE_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_BUTTON'); ?></button>
    </buttons>
</dialog>
