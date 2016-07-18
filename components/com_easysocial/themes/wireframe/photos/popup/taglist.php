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
<div class="btn-group btn-group-sm es-photo-tag-list" data-photo-tag-list>

	<?php if (!$lib->taggable()) { ?>

	<div class="es-media-item-menu-item btn btn-default btn-es dropdown_" data-item-actions-menu>
		<a href="javascript: void(0);" data-bs-toggle="dropdown">
			<?php echo JText::_('COM_EASYSOCIAL_PHOTOS_TAGS'); ?>
		</a>
		<div class="dropdown-menu dropdown-static">
			<div class="es-photo-tag-list-item-group<?php echo (empty($tags)) ? ' empty-tags' : ''; ?>" data-photo-tag-list-item-group>
				<?php if( $tags ){ ?>
					<?php foreach( $tags as $tag ){ ?>
						<?php echo $this->includeTemplate('site/photos/taglist.item', array('tag' => $tag)); ?>
					<?php } ?>
				<?php } ?>
				<span class="empty-tags-hint"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_TAGS_EMPTY_HINT'); ?></span>
			</div>
		</div>
	</div>

	<?php } else { ?>

	<div class="es-media-item-menu-item btn btn-default btn-es" data-photo-tag-button="enable" data-item-actions-menu>
		<a href="javascript: void(0);" data-bs-toggle="">
			<?php echo JText::_('COM_EASYSOCIAL_TAG_PHOTO'); ?>
		</a>
	</div>
	<div class="es-media-item-menu-item btn btn-default btn-es dropdown_" data-item-actions-menu>
		<a href="javascript: void(0);" data-bs-toggle="dropdown"><i class="fa fa-caret-down"></i> </a>
		<div class="dropdown-menu dropdown-static">
			<div class="es-photo-tag-list-item-group<?php echo (empty($tags)) ? ' empty-tags' : ''; ?>" data-photo-tag-list-item-group>
				<?php if( $tags ){ ?>
					<?php foreach( $tags as $tag ){ ?>
						<?php echo $this->includeTemplate('site/photos/taglist.item', array('tag' => $tag)); ?>
					<?php } ?>
				<?php } ?>
				<span class="empty-tags-hint"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_TAGS_EMPTY_HINT'); ?></span>
			</div>
		</div>
	</div>

	<?php } ?>

</div>
