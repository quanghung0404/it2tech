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
<?php echo $video->getMiniHeader();?>

<form action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-videos-form>

    <div class="es-container es-videos es-video-form">
        <div class="es-content">
            <div class="es-videos-content-wrapper es-responsive">

                <h2 class="es-video-group-title"><?php echo JText::_("COM_EASYSOCIAL_VIDEOS_ADD_NEW_VIDEO_TITLE");?></h2>

                <hr>

                <div class="es-video-upload-form">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="video-category" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_CATEGORY');?>
                            </label>
                            <div class="col-sm-8">
                                <select id="video-category" name="category_id" class="form-control input-sm">
                                    <option value=""><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_SELECT_CATEGORY_FOR_VIDEO');?></option>

                                    <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo $category->id;?>"<?php echo $video->category_id == $category->id || $defaultCategory == $category->id ? ' selected="selected"' : '';?>><?php echo $category->title;?></option>
                                    <?php } ?>
                                </select>

                                <div class="help-block"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_CATEGORY_TIPS');?></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="video-title" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TITLE');?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" name="title" placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TITLE_PLACEHOLDER');?>"
                                    value="<?php echo $this->html('string.escape', $video->title);?>" id="video-title"
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="video-desc" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_DESCRIPTION');?>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="video-desc" rows="5" class="form-control input-sm" name="description" placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_DESCRIPTION_PLACEHOLDER');?>"><?php echo $this->html('string.escape', $video->description);?></textarea>
                            </div>
                        </div>

                        <?php if (($this->config->get('video.uploads') && $this->config->get('video.ffmpeg')) && $this->config->get('video.embeds')) { ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TYPE');?>
                            </label>
                            <div class="col-sm-8">
                                <?php if ($this->config->get('video.embeds')) { ?>
                                <label for="video-link" class="radio-inline">
                                    <input id="video-link" type="radio" name="source" value="link" data-video-source <?php echo $video->isLink() ? ' checked="checked"' : '';?>/>
                                    <span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TYPE_EXTERNAL');?></span>
                                </label>
                                <?php } ?>

                                <?php if ($this->config->get('video.uploads') && $this->config->get('video.ffmpeg')) { ?>
                                <label for="video-uploads" class="radio-inline">
                                    <input id="video-uploads" type="radio" name="source" value="upload" data-video-source <?php echo $video->isUpload() ? ' checked="checked"' : '';?>/>
                                    <span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TYPE_UPLOAD');?></span>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } else { ?>
                            <?php if ($this->config->get('video.embeds')) { ?>
                                <input type="hidden" name="source" value="link" />
                            <?php } ?>

                            <?php if ($this->config->get('video.uploads') && $this->config->get('video.ffmpeg')) { ?>
                                <input type="hidden" name="source" value="upload" />
                            <?php } ?>

                        <?php } ?>

                        <?php if ($this->config->get('video.embeds')) { ?>
                        <div class="form-group<?php echo $video->isUpload() ? ' hide' : '';?>" data-form-link data-form-source>
                            <label for="video-link-source" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LINK');?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-sm" name="link"
                                    placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LINK_PLACEHOLDER');?>"
                                    value="<?php echo $video->isLink() ? $video->path : '';?>"
                                    id="video-link-source"
                                />
                            </div>
                        </div>
                        <?php } ?>

                        <?php if ($this->config->get('video.uploads')) { ?>
                        <div class="form-group<?php echo $video->isLink() && $this->config->get('video.embeds') ? ' hide' : '';?>" data-form-upload data-form-source>
                            <label class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_FILE');?>
                            </label>
                            <div class="col-sm-8">
                                <input type="file" name="video" />
                                <div>
                                    <?php echo JText::sprintf('COM_EASYSOCIAL_VIDEOS_VIDEO_FILE_TIPS', $uploadLimit);?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="es-fields-85" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_PEOPLE_IN_THIS_VIDEO');?>
                            </label>
                            <div class="col-sm-8">
                                <div class="textboxlist disabled" data-mentions>

                                    <?php if ($tags) { ?>
                                        <?php foreach ($tags as $tag) { ?>
                                            <div class="textboxlist-item" data-id="<?php echo $tag->getEntity()->id; ?>" data-title="<?php echo $tag->getEntity()->getName(); ?>" data-textboxlist-item>
                                                <span class="textboxlist-itemContent" data-textboxlist-itemContent>
                                                    <img width="16" height="16" src="<?php echo $tag->getEntity()->getAvatar(SOCIAL_AVATAR_SMALL);?>" />
                                                    <?php echo $tag->getEntity()->getName(); ?>
                                                    <input type="hidden" name="items" value="<?php echo $tag->getEntity()->id; ?>" />
                                                </span>
                                                <div class="textboxlist-itemRemoveButton" data-textboxlist-itemRemoveButton>
                                                    <i class="fa fa-remove"></i>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>

                                    <input type="text" autocomplete="off"
                                        disabled
                                        class="textboxlist-textField"
                                        data-textboxlist-textField
                                        placeholder="<?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_START_TYPING');?>"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="es-fields-85" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LOCATION');?>
                            </label>
                            <div class="col-sm-8">
                                <?php echo $this->html('form.location', 'location', $video->getLocation()); ?>
                            </div>
                        </div>

                        <?php if (! $isCluster) { ?>
                        <div class="form-group">
                            <label for="es-fields-85" class="col-sm-3 control-label">
                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_PRIVACY');?>
                            </label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <?php echo $privacy->form($video->id, SOCIAL_TYPE_VIDEOS, $this->my->id, 'videos.view', true, null, array()); ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-actions text-right">
                            <a href="<?php echo $returnLink;?>" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON');?></a>
                            <button class="btn btn-es-primary btn-sm" data-save-button><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON');?></button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($video->uid && $video->type) { ?>
    <input type="hidden" name="uid" value="<?php echo $video->uid;?>" />
    <input type="hidden" name="type" value="<?php echo $video->type;?>" />
    <?php } ?>

    <input type="hidden" name="id" value="<?php echo $video->id;?>" />
    <?php echo $this->html('form.action', 'videos', 'save'); ?>
</form>
