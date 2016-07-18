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
    <width>500</width>
    <height><?php echo $registration && $this->my->guest ? 250 : 200;?></height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{submitButton}" : "[data-submit-button]",

        "{email}" : "[data-subscribe-email]",
        "{name}"  : "[data-subscribe-name]",
        "{username}" : "[data-subscribe-username]",
        "{register}" : "[data-subscribe-register]"
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

            EasyBlog.dialog({
                content : EasyBlog.ajax('site/views/subscription/subscribe', {
                                "type" : "<?php echo $type;?>",
                                "email" : this.email().val(),
                                "name"  : this.name().val(),
                                "username" : this.username().val(),
                                "register" : this.register().is(':checked') ? 1 : 0,
                                "id" : "<?php echo $id;?>",
                                "userId" : "<?php echo $userId;?>"
                          })
            })
        }
    }
    </bindings>
    <title>
        <?php echo $title;?>
    </title>
    <content>
        <p><?php echo $desc;?></p>

        <form method="post" action="<?php echo JRoute::_('index.php');?>" data-form-response>
            <div class="form-group">
                <label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_FULLNAME'); ?></label>
                <div class="col-cell">
                    <input class="form-control input-sm" type="text" id="esfullname" name="esfullname" size="45" value="<?php echo $this->html('string.escape', $this->my->name);?>" data-subscribe-name />
                </div>
            </div>

            <div class="form-group">
                <label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label>
                <div class="col-cell">
                    <input type="text" id="title" name="title" class="form-control input-sm" value="<?php echo $this->html('string.escape', $this->my->email); ?>" data-subscribe-email />
                </div>
            </div>

            <?php if ($registration && $this->my->guest) { ?>
            <div class="form-group">
                <label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_USERNAME'); ?></label>
                <div class="col-cell">
                    <input class="form-control input-sm" type="text" id="esfullname" name="esfullname" size="45" value="<?php echo $this->html('string.escape', $this->my->name);?>" data-subscribe-username />
                </div>
            </div>

            <div class="form-group">
                <div class="col-cell control-label">&nbsp;</div>
                <div class="col-cell">
                    <div class="eb-checkbox">
                        <input type="checkbox" id="subscriptionregister" name="subscriptionregister" value="1" data-subscribe-register />
                        <label for="subscriptionregister">
                            <?php echo JText::_('COM_EASYBLOG_REGISTER_AS_SITE_MEMBER'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <?php } ?>
        </form>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BUTTON'); ?></button>
    </buttons>
</dialog>
