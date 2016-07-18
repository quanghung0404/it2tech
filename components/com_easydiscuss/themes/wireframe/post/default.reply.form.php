<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-post-reply-form">

    <?php if ($post->isLocked()) { ?>
    <div class="o-alert o-alert--warning t-lg-mb--no">
        <i class="icon-lock"></i>
        <?php if (ED::isModerator($post->category_id)) { ?>
            <?php echo JText::_('COM_EASYDISCUSS_POST_IS_CURRENTLY_LOCKED_BUT_MODERATOR'); ?>
        <?php } else { ?>
            <?php echo JText::_('COM_EASYDISCUSS_POST_IS_CURRENTLY_LOCKED'); ?>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if (!$post->isLocked() || ED::isModerator($post->category_id)) { ?>
    <div class="discuss-user-reply" >
        <div class="fd-cf">
            <a name="respond" id="respond"></a>

            <?php if ($access->canReply() && !$post->isUserBanned() && $post->canReply()) { ?>
                <?php echo $composer->getComposer(); ?>
            <?php } else { ?>

                <?php if (!$this->my->id) { ?>
                    <?php echo $this->output('site/forms/reply.login'); ?>
                <?php } elseif ($post->isUserBanned()) { ?>
                    <div class="o-alert o-alert--notice"><?php echo JText::_('COM_EASYDISCUSS_NOT_ALLOWED_YOU_REPLY_BECAUSE_GET_BANNED');?></div>
                <?php } else { ?>
                    <div class="o-alert o-alert--notice"><?php echo JText::_('COM_EASYDISCUSS_NOT_ALLOWED_TO_REPLY');?></div>
                <?php } ?>

            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>