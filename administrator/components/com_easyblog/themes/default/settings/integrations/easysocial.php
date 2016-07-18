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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_LAYOUT_TITLE');?></b>
                <div class="panel-info">
                    <img style="margin: 0 15px 15px 15px;width: 64px;" align="right" src="<?php echo $this->getPathUri('images/vendors');?>/easysocial.png" />
                    <?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL_INFO' ); ?><br><br>
                    <a class="btn btn-primary btn-sm" target="_blank" href="http://stackideas.com/easysocial?from=easyblog"><?php echo JText::_('COM_EASYBLOG_SIGNUP_WITH_EASYSOCIAL');?></a>
                </div>
            </div>
            
            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_badges', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ACHIEVEMENTS'); ?>

                <?php echo $this->html('settings.toggle', 'integrations_easysocial_conversations', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_CONVERSATIONS'); ?>

                <?php echo $this->html('settings.toggle', 'integrations_easysocial_points', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_POINTS'); ?>

                <?php echo $this->html('settings.toggle', 'integrations_easysocial_friends', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_FRIENDS'); ?>

                <?php echo $this->html('settings.toggle', 'integrations_easysocial_followers', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_FOLLOW'); ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PROFILE_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_EASYSOCIAL_STREAM_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_editprofile', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PROFILE_MODIFY_EDIT_PROFILE_LINK'); ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_privacy', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_PRIVACY_INTEGRATION_DESC'); ?>
            </div>
        </div>
	</div>

	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MEDIA_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_MEDIA_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_album', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_ENABLE_MEDIA'); ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_EASYSOCIAL_STREAM_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_stream_newpost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST'); ?>
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_stream_newrss', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_RSS'); ?>
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_stream_featurepost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_FEATURE_POST'); ?>
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_stream_updatepost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_UPDATED_POST'); ?>
                <div class="form-group">
    	            <label for="page_title" class="col-md-5">
    	                <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE'); ?>

    	                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE'); ?>"
    	                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
    	            </label>

    	            <div class="col-md-7">
    					<select id="integrations_easysocial_stream_newpost_source" name="integrations_easysocial_stream_newpost_source" class="form-control" onchange="switchFBPosition();">
    						<option<?php echo $this->config->get( 'integrations_easysocial_stream_newpost_source' ) == 'intro' ? ' selected="selected"' : ''; ?> value="intro"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE_INTRO');?></option>
    						<option<?php echo $this->config->get( 'integrations_easysocial_stream_newpost_source' ) == 'content' ? ' selected="selected"' : ''; ?> value="content"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_POST_SOURCE_CONTENT');?></option>
    					</select>
    	            </div>
    	        </div>
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_stream_newcomment', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_STREAM_NEW_COMMENT'); ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_EASYSOCIAL_NOTIFICATIONS_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_notifications_newpost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_POST'); ?>
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_notifications_newcomment', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_COMMENT'); ?>
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_notifications_ratings', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_NOTIFICATIONS_NEW_RATING'); ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_EASYSOCIAL_INDEXER_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_easysocial_indexer_newpost', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST'); ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_LENGTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_LENGTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_EASYSOCIAL_INDEXER_INDEX_NEW_POST_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" name="integrations_easysocial_indexer_newpost_length" class="form-control text-center" value="<?php echo $this->config->get('integrations_easysocial_indexer_newpost_length');?>" style="text-align:center;" size="5" />
                                <span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
