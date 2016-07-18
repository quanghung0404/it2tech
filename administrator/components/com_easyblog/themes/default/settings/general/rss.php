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
<div class="row form-horizontal">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_RSS');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_RSS_INFO'); ?></div>
			</div>

			<div class="panel-body">
		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
		                <?php echo $this->html('grid.boolean', 'main_rss', $this->config->get('main_rss'));?>
		            </div>
		        </div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<select name="main_rss_content" class="form-control">
							<option value="introtext"<?php echo $this->config->get( 'main_rss_content' ) == 'introtext' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_INTROTEXT' ); ?></option>
							<option value="fulltext"<?php echo $this->config->get( 'main_rss_content' ) == 'fulltext' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_FULLTEXT' ); ?></option>
						</select>
		            </div>
		        </div>
	        </div>
	    </div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_FEEDBURNER');?></b>
				<div class="panel-info">
					<?php echo JText::_('COM_EASYBLOG_SETTINGS_FEEDBURNER_INFO'); ?><br /><br />
					<a href="http://feedburner.com" class="btn btn-primary btn-sm" target="_blank"><?php echo JText::_('COM_EASYBLOG_SETTINGS_FEEDBURNER_APPLY');?></a>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_FEEDBURNER_RSS_URL'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_FEEDBURNER_RSS_URL'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_FEEDBURNER_RSS_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
		                <div class="form-control-static"><?php echo JURI::root();?>index.php?option=com_easyblog&view=latest&format=feed&type=rss</div>
		            </div>
				</div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
		                <?php echo $this->html('grid.boolean', 'main_feedburner', $this->config->get('main_feedburner'));?>
		            </div>
		        </div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_feedburnerblogger', $this->config->get('main_feedburnerblogger'));?>
		            </div>
		        </div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<input type="text" name="main_feedburner_url" class="form-control" value="<?php echo $this->config->get('main_feedburner_url');?>" size="60" />
		            </div>
		        </div>
		    </div>
	    </div>
	</div>

</div>
