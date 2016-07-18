<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
				<b>
					<i class="fa fa-facebook-square"></i>&nbsp; 
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK');?>

					<?php if( $this->config->get('integrations_facebook_api_key') && $this->config->get('integrations_facebook_secret_key') && $this->config->get('integrations_facebook') && $facebook ){ ?>
					<span class="label label-success ml-10">
						<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_CONFIGURED');?>
					</span>
					<?php } ?>
				</b>
			</div>
			<div class="panel-body">
				<p>
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_DESC');?>
				</p>
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=autoposting&layout=facebook');?>" class="btn btn-primary btn-sm">
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_CONFIGURE');?>
				</a>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b>
					<i class="fa fa-twitter-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER');?>
					<?php if( $this->config->get('integrations_twitter_api_key') && $this->config->get('integrations_twitter_secret_key') && $this->config->get('integrations_twitter') && $twitter ){ ?>
					<span class="label label-success ml-10">
						<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_CONFIGURED');?>
					</span>
					<?php } ?>
				</b>
			</div>

			<div class="panel-body">
				<p>
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_DESC');?>
				</p>
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=autoposting&layout=twitter');?>" class="btn btn-primary btn-sm">
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_CONFIGURE');?>
				</a>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b>
					<i class="fa fa-linkedin-square"></i>&nbsp; 
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN');?>
					<?php if( $this->config->get('integrations_linkedin_api_key') && $this->config->get('integrations_linkedin_secret_key') && $this->config->get('integrations_linkedin') && $linkedin ){ ?>
					<span class="label label-success ml-10">
						<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_CONFIGURED');?>
					</span>
					<?php } ?>
				</b>
			</div>

			<div class="panel-body">
				<p>
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_DESC');?>
				</p>

				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=autoposting&layout=linkedin');?>" class="btn btn-primary btn-sm">
					<?php echo JText::_('COM_EASYBLOG_AUTOPOST_CONFIGURE');?>
				</a>
			</div>
		</div>
	</div>
</div>