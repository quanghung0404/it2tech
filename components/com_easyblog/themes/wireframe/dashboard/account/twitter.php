<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-box">
	<div class="eb-box-head">
		<i class="fa fa-twitter-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TWITTER_SETTINGS_TITLE');?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TWITTER_SETTINGS_DESC');?></p>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS');?></label>
				<div class="col-md-7">

                    <?php if ($twitter->id && $twitter->request_token && $twitter->access_token) { ?>
                    <label>
                        <a href="<?php echo EB::_('index.php?option=com_easyblog&task=oauth.revoke&client=' . EBLOG_OAUTH_TWITTER);?>" class="btn btn-default btn-sm">
                            <i class="fa fa-close"></i>&nbsp; <?php echo JText::_( 'COM_EASYBLOG_OAUTH_REVOKE_ACCESS' ); ?>
                        </a>
                        <div class="small"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_NOTICE_TWITTER_REVOKE')?></div>
                    </label>
                    <?php } else { ?>
                    <label class="mbl"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?></label>
                    <div class="mtm">
                        <a href="javascript:void(0);" data-oauth-signup data-client="twitter">
                            <img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/twitter_signon.png" border="0" />
                        </a>
                    </div>
                    <?php } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_MESSAGE');?></label>
				<div class="col-md-8">
                    <textarea id="integrations_twitter_message" name="integrations_twitter_message" class="form-control"><?php echo (empty($twitter->message)) ? $this->config->get('main_twitter_message', JText::_('COM_EASYBLOG_EASYBLOG_TWITTER_AUTOPOST_MESSAGE') ) : $twitter->message; ?></textarea>
                    <div class="small"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_NOTICE_TWITTER_CHAR_LIMIT')?></div>
				</div>
			</div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLED_BY_DEFAULT');?></label>
                <div class="col-md-8">
                    <?php echo $this->html('grid.boolean', 'integrations_twitter_auto', $twitter->auto); ?>
                </div>
            </div>
		</div>
	</div>
</div>