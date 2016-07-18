<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-story-video-form is-waiting" data-video-form>

    <div class="es-video-item-wrap fd-cf video-result" data-result>
        <a class="es-video-item-remove" data-remove-video><i class="fa fa-times"></i></a>

        <div class="es-video-item es-media-item">
            <div class="es-video" data-video-preview-image>
            </div>
        </div>
        <div class="es-video-item-content">
            <div class="es-video-item-title" data-video-preview-title><?php echo JText::_('COM_EASYSOCIAL_STORY_VIDEOS_ENTER_TITLE');?></div>
            <div class="es-video-item-title-textbox">
                <input type="text"
                       class="es-story-link-title-textfield form-control input-sm"
                       data-video-title
                       placeholder="<?php echo JText::_('COM_EASYSOCIAL_STORY_VIDEOS_TITLE_PLACEHOLDER'); ?>"
                       value="" />
            </div>

            <div class="es-video-item-desp" data-video-preview-description><?php echo JText::_('COM_EASYSOCIAL_STORY_VIDEOS_ENTER_DESC');?></div>
            <div class="es-video-item-desp-textbox">
                <textarea class="es-story-link-description-textfield form-control input-sm"
                          data-video-description
                          placeholder="<?php echo JText::_('COM_EASYSOCIAL_STORY_VIDEOS_DESC_PLACEHOLDER'); ?>"
                          ></textarea>
            </div>
        </div>
    </div>

    <?php if ($this->config->get('video.uploads') && $this->config->get('video.ffmpeg')) { ?>
    <div class="es-story-video-progress-wrap video-progress" data-progress>
        <span class="es-story-video-status-text"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_ENCODING_YOUR_VIDEO');?> <span data-video-uploader-progress-text>0%</span></span>
        <div class="progress mb-5">
            <div class="bar" style="width: 0%" data-video-uploader-progress-bar></div>
        </div>    
    </div>
    <?php } ?>

    <div class="video-form">

        <?php if ($this->config->get('video.uploads') && $this->config->get('video.ffmpeg')) { ?>
        <div class="es-video-upload-container">
            <div data-video-uploader class="es-video-content">
                <div data-video-uploader-dropsite>
                    <div data-video-uploader-button class="es-video-upload-button">
                        <span>
                            <b class="add-hint">
                                <i class="fa fa-upload"></i> <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CLICK_OR_DROP_VIDEO');?>
                            </b>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (($this->config->get('video.uploads') && $this->config->get('video.ffmpeg')) && $this->config->get('video.embeds')) { ?>
        <div class="es-video-form-divider">
            <span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_OR_PASTE_VIDEO_LINK');?></span>
        </div>
        <?php } ?>

        <?php if ($this->config->get('video.embeds')) { ?>
        <div class="es-video-share-container">
            <div class="form-group">
                <div class="input-group input-group-sm">
                    <input type="text" name="video_link" class="form-control input-sm" data-video-link placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LINK_PLACEHOLDER');?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-default insert-button" type="button" data-insert-video>
                        	<i class="loading-indicator fd-small"></i> <span><?php echo JText::_('COM_EASYSOCIAL_INSERT_VIDEO_BUTTON');?></span>
                        </button>
                    </span>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="video-category">
        <div class="form-inline">
            <div class="form-group">
                <select name="video-category" class="form-control input-sm" data-video-category>
                    <option value="0"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_SELECT_CATEGORY');?></option>
                    <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category->id;?>"><?php echo $category->title;?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>

</div>


