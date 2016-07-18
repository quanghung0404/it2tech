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
<div class="es-comments-wrap"
    <?php if (!$comments && $hideEmpty) { ?>style="display: none;"<?php } ?>
    data-comments="<?php echo $group; ?>-<?php echo $element; ?>-<?php echo $verb; ?>-<?php echo $uid; ?>"
    data-group="<?php echo $group; ?>"
    data-element="<?php echo $element; ?>"
    data-verb="<?php echo $verb; ?>"
    data-uid="<?php echo $uid; ?>"
    data-count="<?php echo $count; ?>"
    data-total="<?php echo $total; ?>"
    data-url="<?php echo empty($url) ? '' : $url; ?>"
    data-streamid="<?php echo empty($streamid) ? '' : $streamid; ?>"
>
    <?php if ($this->access->allowed('comments.read')) { ?>
        <?php if ($total > $count) { ?>
            <div class="es-comments-control" data-comments-control>
                <div class="es-comments-load" data-comments-load class="es-comments-load">
                    <a class="link-loadmore" data-comments-load-loadMore href="javascript:void(0);">
                        <?php echo JText::_('COM_EASYSOCIAL_COMMENTS_ACTION_LOAD_MORE'); ?>
                        <div class="pull-right es-comments-stats" data-comments-stats>
                            <?php echo JText::sprintf('COM_EASYSOCIAL_COMMENTS_LOADED_OF_TOTAL', $count, $total); ?>
                        </div>
                    </a>
                </div>
            </div>
        <?php } ?>

        <ul class="fd-reset-list es-comments" data-comments-list>
            <?php foreach ($comments as $comment) { ?>
                <?php echo $comment->renderHTML(array('deleteable' => $deleteable)); ?>
            <?php } ?>
        </ul>
    <?php } ?>

    <?php if (!$hideForm && $this->access->allowed('comments.add') && $this->my->id) { ?>
    <div class="es-comments-form" data-comments-form>
        <div class="es-avatar es-avatar-sm" data-comments-item-avatar>
            <img src="<?php echo $this->my->getAvatar(); ?>" width="64" height="64" />
        </div>
        <div class="es-form" data-comment-form-header>
            <div data-comment-form-editor-area>
                <div data-uploader-form>
                    <div class="mentions">
                        <div data-mentions-overlay data-default=""></div>
                        <textarea class="form-control input-sm" row="1" name="message" 
                            data-mentions-textarea
                            data-default=""
                            data-initial="0"
                            data-comments-form-input
                            placeholder="<?php echo JText::_('COM_EASYSOCIAL_COMMENTS_FORM_PLACEHOLDER' , true);?>"></textarea>

                        <b class="es-form-attach">
                            <?php if ($this->config->get('comments.attachments')) { ?>
                                <label class="es-input-photo fa fa-camera" for="input-file" data-uploader-browse>&nbsp;</label>
                            <?php } ?>

                            <?php if ($this->config->get('comments.smileys')) { ?>
                                <label class="es-input-smiley fa fa-smile-o" data-comment-smileys>
                                    <?php echo ES::smileys()->html();?>
                                </label>
                            <?php } ?>
                        </b>
                    </div>

                    <div class="attachments clearfix" data-comment-attachment-queue></div>
                </div>
            </div>
        </div>
        <div class="es-form-footer mt-5">
            <?php if ($this->config->get('comments.submit')) { ?>
            <a class="btn btn-es btn-sm pull-right" href="javascript:void(0);" data-comments-form-submit><?php echo JText::_('COM_EASYSOCIAL_COMMENTS_ACTION_SUBMIT'); ?></a>
            <?php } ?>
            <span class="label" style="display: none;" data-comments-form-status></span>
        </div>

    </div>
    <?php } ?>

    <div class="hide" data-comment-attachment-template>
        <div class="figure" data-comment-attachment-item>
            <div class="attachment" data-comment-attachment-background>
                <div class="upload-progress">
                    <div class="progress progress-striped active">
                        <div class="upload-progress-bar bar progress-bar-info" style="width: 0%;" data-comment-attachment-progress-bar></div>
                    </div>
                </div>
                <a href="javascript:void(0);" class="attachment-cancel fa fa-times" data-comment-attachment-remove></a> 
            </div>
        </div>
    </div>

</div>
