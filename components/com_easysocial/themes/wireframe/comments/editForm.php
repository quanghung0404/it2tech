<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-story-text">
    <div class="es-comments-form es-story-textbox mentions-textfield" data-story-textbox>
        <div class="mentions">
            <div data-mentions-overlay><?php echo $overlay; ?></div>
            <textarea class="es-story-textfield form-control" name="content" 
                data-comments-item-edit-input
                data-story-textField
                data-mentions-textarea
                data-initial="0"
            ><?php echo $comment; ?></textarea>
        </div>

        <?php if ($this->config->get('comments.smileys')) { ?>
        <b class="es-form-attach">
            <label class="es-input-smiley fa fa-smile-o" data-comment-smileys>
                <?php echo ES::smileys()->html();?>
            </label>
        </b>
        <?php } ?>

    </div>
</div>

<div class="es-stream-editor-actions clearfix">
    <a href="javascript:void(0);" class="btn btn-es-danger btn-sm pull-left" data-comments-item-edit-cancel><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON');?></a>
    <a href="javascript:void(0);" class="btn btn-es-primary btn-sm pull-right" data-comments-item-edit-submit>
        <i class="fa fa-floppy-o"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_COMMENTS_ACTION_SAVE');?>
    </a>
</div>
