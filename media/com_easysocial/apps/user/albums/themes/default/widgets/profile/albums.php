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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-widget">
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_('APP_ALBUMS_PROFILE_WIDGET_TITLE'); ?>
		</div>
		<?php if ($params->get('showcount', $appParams->get('showcount', true))){ ?>
		<span class="widget-label">(<?php echo $total;?>)</span>
		<?php } ?>

		<?php if (!empty($albums)){ ?>
			<a class="fd-small pull-right" href="<?php echo FRoute::albums(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>"><?php echo JText::_('APP_ALBUMS_PROFILE_WIDGET_VIEW_ALL');?></a>
		<?php } ?>
	</div>
	<div class="es-widget-body">
		<ul class="widget-list-grid">
			<?php for ($i = 0; $i < $limit; $i++) { ?>
			<?php if (!empty($albums[$i])) { ?>
				<?php $album = $albums[$i]; ?>
				<li data-es-photo-group="album:<?php echo $album->id; ?>">
					<a href="<?php echo $album->getPermalink();?>" class="es-avatar"
						data-original-title="<?php echo $this->html('string.escape', $album->get('title'));?>"
						data-es-provide="tooltip"
						data-placement="bottom"
						<?php if ($album->cover_id && $privacy->validate('photos.view' , $album->cover_id,  SOCIAL_TYPE_PHOTO, $user->id)) { ?>
						data-es-photo="<?php echo $album->cover_id; ?>"
						<?php } ?>
					>
						<img alt="<?php echo $this->html('string.escape', $album->get('title'));?>" src="<?php echo $album->getCover('square');?>" />
					</a>
				</li>
			<?php } ?>
			<?php } ?>
		</ul>
	</div>
</div>
