<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_VIDEOS_TITLE');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-sm-5">
                            	<div class="input-group">
                                    <input type="text" name="max_video_width" class="form-control text-center" value="<?php echo $this->config->get('max_video_width' );?>" />
                                    <span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
            					</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" name="max_video_height" class="form-control text-center" value="<?php echo $this->config->get('max_video_height' );?>" />
                                    <span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>