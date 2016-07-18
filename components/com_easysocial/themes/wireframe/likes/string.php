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
<i class="icon-es-heart"></i>
<?php if( $total >= 1 ){ ?>

	<?php if( $total == 1 ){ ?>
		<?php echo JText::sprintf( $language , $users[ 0 ]->getPermalink() , $users[ 0 ]->getName() ); ?>
	<?php } ?>

	<?php if( $total == 2 ){ ?>
		<?php echo JText::sprintf( $language , $users[ 0 ]->getPermalink() , $users[ 0 ]->getName() , $users[ 1 ]->getPermalink() , $users[ 1 ]->getName() ); ?>
	<?php } ?>

	<?php if( $total == 3 ){ ?>
		<?php echo JText::sprintf( $language , $users[ 0 ]->getPermalink() , $users[ 0 ]->getName() , $users[ 1 ]->getPermalink() , $users[ 1 ]->getName() , $users[ 2 ]->getPermalink() , $users[ 2 ]->getName() ); ?>
	<?php } ?>

	<?php if( $total == 4 ){ ?>
		<?php echo JText::sprintf( $language , $users[ 0 ]->getPermalink() , $users[ 0 ]->getName() , $users[ 1 ]->getPermalink() , $users[ 1 ]->getName() , $users[ 2 ]->getPermalink() , $users[ 2 ]->getName() , $users[ 3 ]->getPermalink() , $users[ 3 ]->getName() ); ?>
	<?php } ?>

	<?php if( $total > 4 ){ ?>
		<?php echo JText::sprintf( $language , $users[ 0 ]->getPermalink() , $users[ 0 ]->getName() , $users[ 1 ]->getPermalink() , $users[ 1 ]->getName() , $users[ 2 ]->getPermalink() , $users[ 2 ]->getName() , $uid , $group, $element , $verb, $remainder ); ?>
	<?php } ?>

<?php } ?>
