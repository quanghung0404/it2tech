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
		<i class="fa fa-linkedin-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_TITLE');?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_SETTINGS_DESC');?></p>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS');?></label>
				<div class="col-md-7">
                    <?php if ($linkedin->id) {?>
                        <a href="<?php echo EBR::_('index.php?option=com_easyblog&task=oauth.revoke&client=' . EBLOG_OAUTH_LINKEDIN);?>" class="btn btn-default btn-sm">
                            <i class="fa fa-close"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_OAUTH_REVOKE_ACCESS'); ?>
                        </a>
                    <?php } else { ?>
                        <label><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?></label>
                        <a href="javascript:void(0);" data-oauth-signup data-client="linkedin">
                            <img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/linkedin_signon.png" border="0" alt="here" />
                        </a>
                    <?php } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLED_BY_DEFAULT');?></label>
				<div class="col-md-8">
					<?php echo $this->html('grid.boolean', 'integrations_linkedin_auto', $linkedin->auto); ?>
				</div>
			</div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE');?></label>
                <div class="col-md-8">
                    <?php echo $this->html('grid.boolean', 'integrations_linkedin_private', $linkedin->private); ?>

                    <div class="small">
                        <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE_DESC');?>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>