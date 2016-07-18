<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
    <height>150</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{suggest}"     : "[data-friends-suggest]",
        "{sendInvite}"  : "[data-invite-button]",
        "{errorMessage}": "[data-error-message]"
    }
    </selectors>
    <bindings type="javascript">
    {
        init: function()
        {
            this.suggest()
                .addController(
                    "EasySocial.Controller.Friends.Suggest", {
                        exclusion: <?php echo FD::json()->encode($exclusion); ?>,
                        type: "inviteevent"
                    }
               );
        },
        "{closeButton} click": function()
        {
            this.parent.close();
        },
        "{sendInvite} click" : function()
        {
            this.errorMessage().hide();

            var items = this.suggest().textboxlist('controller').getAddedItems();

            if (items.length === 0) {
                this.errorMessage().show();
                return;
            }

            var uid = [];

            $.each(items, function(index, item) {
                uid.push(item.id);
            });

            EasySocial.dialog({
                content: '<div class="fd-loading"><span><?php echo JText::_( 'COM_EASYSOCIAL_LOADING' );?></span></div>'
            });

            EasySocial.ajax('site/controllers/events/inviteFriends', {
                id: <?php echo $event->id;?>,
                uid: uid
            }).done(function(content) {
                EasySocial.dialog({
                    content: content
                });

                setTimeout(function() {
                    EasySocial.dialog().close();
                }, 2000);
            }).fail(function(error) {
                EasySocial.dialog({
                    content: error.message
                });
            });
        }
    }
    </bindings>
    <title><?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_DIALOG_INVITE_TO_EVENT_TITLE', $event->getName()); ?></title>
    <content>
        <p class="alert alert-danger" style="display: none;" data-error-message><?php echo JText::_('COM_EASYSOCIAL_EVENTS_DIALOG_INVITE_TO_EVENT_ERROR_NO_USERS'); ?></p>
        <p class="mt-5">
            <?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_DIALOG_INVITE_TO_EVENT_CONTENT', $event->getName());?>
        </p>

        <div class="textboxlist controls disabled" data-friends-suggest>
            <input type="text" disabled autocomplete="off" class="participants textboxlist-textField" placeholder="<?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_START_TYPING');?>" data-textboxlist-textField data-textboxlist-textField />
        </div>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-sm btn-es"><?php echo JText::_('COM_EASYSOCIAL_CLOSE_BUTTON'); ?></button>
        <button data-invite-button type="button" class="btn btn-sm btn-es-primary"><?php echo JText::_('COM_EASYSOCIAL_SEND_INVITATIONS_BUTTON'); ?></button>
    </buttons>
</dialog>
