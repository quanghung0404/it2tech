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
	            <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_TITLE');?></b>
	            <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_INFO'); ?></div>
            </div>

            <div class="panel-body">
	            <?php echo $this->html('settings.toggle', 'main_twitter_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
	            <?php echo $this->html('settings.toggle', 'main_twitter_button_frontpage', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHOW_ON_FRONTPAGE'); ?>
                <?php echo $this->html('settings.toggle', 'main_twitter_cards', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_CARDS_ENABLE'); ?>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_TYPE'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_TYPE'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_TYPE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
	  						$imageCard = array();
							$imageCard[] = JHTML::_('select.option', 'summary_large_image', JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_SUMMARY_LARGE'));
							$imageCard[] = JHTML::_('select.option', 'summary', JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_IMAGE_CARD_SUMMARY'));

							$showdet = JHTML::_('select.genericlist', $imageCard, 'main_twitter_cards_type', 'class="form-control"', 'value', 'text', $this->config->get('main_twitter_cards_type' , 'summary_large_image' ) );
							echo $showdet;
						?>
		            </div>
		        </div>

	            <?php echo $this->html('settings.text', 'main_twitter_button_via_screen_name', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME', '', '', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_VIA_SCREEN_NAME_EXAMPLE')); ?>
                
	        </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_DESC'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'main_twitter_analytics', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_ENABLE'); ?>

                <div>
                    <span class="label label-important"><?php echo JText::_('COM_EASYBLOG_NOTE');?></span> <?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_NOTE');?>
                </div>
            </div>
        </div>
        
	</div>

	<div class="col-lg-6">

        <div class="panel">
        	<div class="panel-head">
	            <b><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_TITLE');?></b>
	            <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_INFO'); ?></div>
            </div>

            <div class="panel-body">
	            <?php echo $this->html('settings.toggle', 'integrations_twitter_microblog', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_ENABLE'); ?>

	            <?php echo $this->html('settings.textarea', 'integrations_twitter_microblog_hashes', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS', '', '', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_SEARCH_HASHTAGS_INSTRUCTIONS'); ?>

	            <?php echo $this->html('settings.categories', 'integrations_twitter_microblog_category', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_CATEGORY'); ?>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_PUBLISH_STATE'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_PUBLISH_STATE'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_PUBLISH_STATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
	  						$publishFormat = array();
							$publishFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_UNPUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SCHEDULED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_DRAFT_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $publishFormat, 'integrations_twitter_microblog_publish', 'class="form-control"', 'value', 'text', $this->config->get('integrations_twitter_microblog_publish' , '1' ) );
							echo $showdet;
						?>
		            </div>
		        </div>

		        <?php echo $this->html('settings.toggle', 'integrations_twitter_microblog_frontpage', 'COM_EASYBLOG_INTEGRATIONS_TWITTER_MICROBLOGGING_FRONTPAGE'); ?>
	        </div>
        </div>
	</div>
</div>