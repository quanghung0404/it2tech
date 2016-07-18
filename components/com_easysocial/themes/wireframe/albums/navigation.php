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
<div class="pagination es-albums-pagination">
	<ul class="pagination es-albums-nav<?php echo $action == 'editing' || $action == 'viewing' ? ' col-3' : ' col-2';?>">
		<li class="<?php echo $action == 'listing' ? 'active' : '';?>">
			<a href="<?php echo FRoute::albums();?>">
				<i class="fa fa-photo"></i>
				<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_ALL_MY_ALBUMS'); ?>
			</a>
		</li>

		<?php if( $action == 'listing' || $action == 'creating'){ ?>
		<li class="<?php echo $action == 'creating' ? 'active' : '';?>">
			<a href="<?php echo FRoute::albums( array( 'layout' => 'form' ) ); ?>">
				<i class="fa fa-new"></i>
				<?php echo JText::_('COM_EASYSOCIAL_CREATE_ALBUM'); ?>
			</a>
		</li>
		<?php } ?>

		<?php if( $action == 'editing' || $action == 'viewing' ){ ?>
		<li class="<?php echo $action == 'viewing' ? 'active' : '';?>">
			<a href="<?php echo $album->getPermalink(); ?>">
				<i class="fa fa-grid-view"></i>
				<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_VIEW_ALBUM'); ?>
			</a>
		</li>
		<?php } ?>

		<?php if( $action == 'viewing' || $action == 'editing' ){ ?>
		<li class="<?php echo $action == 'editing' ? 'active' : '';?>">
			<a href="<?php echo FRoute::albums( array( 'layout' => 'form' , 'id' => $album->getAlias() ); ?>">
				<i class="fa fa-pencil"></i>
				<?php echo JText::_('COM_EASYSOCIAL_EDIT_ALBUM'); ?>
			</a>
		</li>
		<?php } ?>
	</ul>
</div>

<div class="row">
<?php if( $action == 'editing' ){ ?>
<h3><?php echo JText::_("COM_EASYSOCIAL_EDIT_ALBUM"); ?></h3>
<?php } ?>

<?php if( $action == 'creating' ){ ?>
<h3><?php echo JText::_("COM_EASYSOCIAL_CREATE_ALBUM"); ?></h3>
<?php } ?>
</div>
