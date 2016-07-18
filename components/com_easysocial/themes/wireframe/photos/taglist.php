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
<div data-photo-tag-list class="es-photo-tag-list">
	<label><?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_IN_THIS_PHOTO' ); ?></label>

	<div data-photo-tag-list-item-group class="es-photo-tag-list-item-group<?php echo (empty($tags)) ? ' empty-tags' : ''; ?>">
		<?php if( $tags ){ ?>
			<?php foreach( $tags as $tag ){ ?>
				<?php echo $this->includeTemplate('site/photos/taglist.item', array('tag' => $tag)); ?>
			<?php } ?>
		<?php } ?>
		<?php if (!$lib->taggable()) { ?>
		<span class="empty-tags-hint"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_TAGS_EMPTY_HINT'); ?></span>
		<?php } ?>
	</div>

	<?php if ($lib->taggable()) { ?>
	<div data-photo-tag-button="enable" class="btn btn-es btn-media es-photo-tag-button"><a href="javascript: void(0);"><i class="fa fa-plus"></i> <?php echo JText::_("COM_EASYSOCIAL_TAG_PHOTO"); ?></a></div>
	<?php } ?>
</div>
