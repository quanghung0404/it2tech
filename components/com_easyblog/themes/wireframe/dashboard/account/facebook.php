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
		<i class="fa fa-facebook-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_FACEBOOK_SETTINGS_TITLE');?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FACEBOOK_SETTINGS_DESC');?></p>
		<div class="form-horizontal">
            <?php if ($this->config->get('main_facebook_ogauthor')) { ?>
            <div class="form-group">
                <label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_FACEBOOK_PROFILE_URL'); ?></label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="facebook_profile_url" id="facebook_profile_url" value="<?php echo $params->get('facebook_profile_url');?>" />
                    <p class="small">
                        <?php echo JText::_('COM_EASYBLOG_FACEBOOK_PROFILE_URL_INFO'); ?>
                    </p>
                </div>
            </div>
            <?php } ?>

            <?php if ($this->acl->get('update_facebook') && $this->config->get('integrations_facebook') && $this->config->get('integrations_facebook_centralized_and_own')) {?>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS');?></label>
				<div class="col-md-7">
					<?php if ($facebook->id) { ?>
        				
        				<a href="<?php echo EBR::_('index.php?option=com_easyblog&task=oauth.revoke&client=' . EBLOG_OAUTH_FACEBOOK );?>" class="btn btn-default btn-sm">
        					<i class="fa fa-close"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_OAUTH_REVOKE_ACCESS'); ?>
        				</a>

        				<hr />

						<p style="margin:8px 0 8px 0;" class="small">
							<?php echo JText::_('COM_EASYBLOG_FACEBOOK_EXPIRE_TOKEN');?> <strong><?php echo $facebook->getExpireDate()->format(JText::_('DATE_FORMAT_LC3')); ?></strong>.
						</p>
						
						<a href="javascript:void(0);" class="btn btn-default btn-sm" id="facebook-login">
							<i class="fa fa-refresh"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_FACEBOOK_RENEW_YOUR_TOKEN' );?>
						</a>
        			<?php } else { ?>
        				<label class="mbs"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_ACCESS_DESC');?></label>
        				<div>
        					<a href="javascript:void(0);" data-oauth-signup data-client="facebook">
                                <img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/facebook_signon.png" border="0" alt="here" />
                            </a>
        				</div>
        	    	<?php } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLED_BY_DEFAULT');?></label>
				<div class="col-md-8">
					<?php echo $this->html('grid.boolean', 'integrations_facebook_auto', $facebook->auto); ?>
				</div>
			</div>
            <?php } ?>

		</div>
	</div>
</div>