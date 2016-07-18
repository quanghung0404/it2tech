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
<div id="fd" class="eb eb-mod mod-easyblogsubscribers<?php echo $params->get('moduleclass_sfx') ?>">
	<div class="mod-thumbs">
		<?php if ($subscribers['users']) { ?>
			<?php foreach ($subscribers['users'] as $subscriber) { ?>
			<div>
				<a href="<?php echo $subscriber->getPermalink();?>" class="mod-avatar">
					<img src="<?php echo $subscriber->getAvatar();?>" />
				</a>
			</div>
			<?php } ?>
		<?php } ?>
	</div>

	<div class="mod-hr"></div>

	<?php if ($subscribers['guests']) { ?>
		<p>
			<?php echo JText::sprintf('MOD_EASYBLOGSUBSCRIBERS_TOTAL_GUESTS', count($subscribers['guests'])); ?>
		</p>
	<?php } ?>

	<div>
	<?php if ($subscribed) { ?>
		<a href="javascript:void(0);" class="btn btn-danger btn-sm" data-blog-unsubscribe data-subscription-id="<?php echo $subscribed;?>" data-return="<?php echo $return;?>">
			<?php echo JText::_('MOD_EASYBLOGSUBSCRIBERS_UNFOLLOW');?>
		</a>
	<?php } else { ?>
		<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-blog-subscribe data-id="<?php echo $id;?>" data-type="<?php echo $type;?>">
			<?php echo JText::_('MOD_EASYBLOGSUBSCRIBERS_FOLLOW');?>
		</a>
	<?php } ?>
	</div>
</div>

