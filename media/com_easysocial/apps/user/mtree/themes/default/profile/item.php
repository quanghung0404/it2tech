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
<div class="media">
	<?php if ($params->get('listing_picture', true)) { ?>
	<div class="media-object pull-left">
		<a href="<?php echo $item->hyperlink;?>">
			<img src="<?php echo $item->link_avatar;?>" width="120" />
		</a>
	</div>
	<?php } ?>

	<div class="media-body">
		<h4>
			<a href="<?php echo $item->hyperlink;?>"><?php echo $item->link_name;?></a>
		</h4>

		<?php if ($params->get('listing_category', true)) { ?>
		<div class="item-meta" style="margin-bottom: 10px;">
			<span>
				<a href="<?php echo $item->categoryLink;?>"><?php echo $item->category;?></a>
			</span>
		</div>
		<?php } ?>

		<?php if ($params->get('listing_desc', true)) { ?>
		<div class="muted" style="margin-bottom: 20px;"><?php echo $this->html('string.truncate', $item->link_desc, 200);?></div>
		<?php } ?>

		<?php if ($params->get('listing_ratings', true)) { ?>
		<div class="ratings" style="height: 32px;">
			<?php echo $item->ratings;?>

			<a href="<?php echo $item->hyperlink;?>" class="btn btn-es btn-mini">
				<?php echo $item->link_votes;?> <?php echo JText::_('APP_USER_MTREE_VOTES'); ?>
			</a>
		</div>
		<?php } ?>

	</div>
</div>