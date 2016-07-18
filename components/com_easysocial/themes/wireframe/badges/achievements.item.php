<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li data-id="<?php echo $badge->id;?>"
	data-es-provide="tooltip"
	data-original-title="<?php echo $this->html( 'string.escape' , $badge->custom_message ? $badge->custom_message : $badge->get( 'description' ) );?>"
	data-placement="bottom"
>
	<div class="achievement-badge">
		<a href="<?php echo $badge->getPermalink();?>">
			<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" title="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" />
		</a>
	</div>
	<div class="achievement-title">
		<a href="<?php echo $badge->getPermalink();?>"><?php echo $badge->get( 'title' ) ; ?></a>
	</div>

	<div class="achievement-date">
		<?php echo $badge->getAchievedDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
	</div>
</li>
