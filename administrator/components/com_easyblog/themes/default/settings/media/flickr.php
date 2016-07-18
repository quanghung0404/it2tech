<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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
                <b><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FLICKR_TITLE');?></b>
                <p class="panel-info">
    				<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FLICKR_INFO');?>
    			</p>
            </div>

            <div class="panel-body">
    			<div style="line-height:28px;height:28px;">
    				<a href="http://www.flickr.com/services/apps/create/" target="_blank"><?php echo JText::_('COM_EASYBLOG_FLICKR_CREATE_APP');?></a>

    				<?php echo JText::_('COM_EASYBLOG_OR'); ?>

    				<a href="http://stackideas.com/docs/easyblog/administrators/integrations/integrating-with-flickr" target="_blank"><?php echo JText::_('COM_EASYBLOG_FLICKR_VIEW_DOC');?></a>
    			</div>

    			<?php echo $this->html('settings.toggle', 'layout_media_flickr', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_FLICKR'); ?>

    			<div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_API_KEY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_API_KEY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="integrations_flickr_api_key" class="form-control" value="<?php echo $this->config->get('integrations_flickr_api_key');?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_SECRET_KEY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_SECRET_KEY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_FLICKR_SECRET_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="integrations_flickr_secret_key" class="form-control" value="<?php echo $this->config->get('integrations_flickr_secret_key');?>" />
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
