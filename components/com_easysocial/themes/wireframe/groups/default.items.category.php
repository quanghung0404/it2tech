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
<?php if( isset( $activeCategory ) && $activeCategory && $this->template->get( 'groups_category_header' , true ) ){ ?>
<?php if ($showCategoryHeader) { ?>
<div class="category-listed-header">
	<div class="media">
		<div class="media-object pull-left">
			<img src="<?php echo $activeCategory->getAvatar();?>" class="es-avatar" title="<?php echo $this->html( 'string.escape' , $activeCategory->get( 'title' ) );?>" />
		</div>
		<div class="media-body">
			<h3 class="h3 es-title-font mt-10"><?php echo $activeCategory->get( 'title' ); ?></h3>
		</div>
	</div>

	<p class="fd-small">
		<?php echo $activeCategory->get( 'description' ); ?>
	</p>

	<div class="mt-15">
		<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $activeCategory->getAlias() ) );?>" class="btn btn-es-primary btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_MORE_INFO_CATEGORY' ); ?> &rarr;</a>
	</div>

</div>

<hr />
<?php } ?>
<?php } ?>
