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
			<?php echo JText::_('APP_GROUP_PHOTOS_WIDGET_TITLE_ALBUMS'); ?>
		</div>

		<?php if( $total != 0 ){ ?>
		<span class="widget-label">(<?php echo $total;?>)</span>
		<?php } ?>

		<?php if ($albums) { ?>
			<a class="fd-small pull-right" href="<?php echo FRoute::albums( array( 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP ) );?>"><?php echo JText::_('APP_GROUP_PHOTOS_WIDGET_VIEW_ALL_ALBUMS');?></a>
		<?php } ?>
	</div>
	<div class="es-widget-body">
		<ul class="widget-list-grid">
			<?php if ($albums) { ?>
				<?php foreach ($albums as $album) { ?>
					<li>
						<a href="<?php echo $album->getPermalink();?>"
							class="es-avatar es-avatar-default"
							data-original-title="<?php echo $this->html( 'string.escape' , $album->get( 'title' ) );?>"
							data-es-provide="tooltip"
							data-placement="bottom"
						>
							<img alt="<?php echo $this->html('string.escape', $album->get('title'));?>" src="<?php echo $album->getCover();?>" />
						</a>
					</li>
				<?php } ?>
			<?php } else { ?>
			<li>
				<div class="fd-small">
					<?php echo JText::_( 'APP_GROUP_PHOTOS_WIDGET_NO_ALBUMS_YET' ); ?>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
