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
<?php if( !$actor->isBlock() ) { ?>
<a href="<?php echo $actor->getPermalink();?>" alt="<?php echo $this->html( 'string.escape' , $actor->getName() );?>"><?php echo $actor->getName(); ?></a>
<?php } else { ?>
<?php echo $actor->getName(); ?>
<?php } ?>

<?php if( $target && $actor->id != $target->id  ){ ?>
<i class="fa fa-caret-right"></i>
	<?php if( ! $target->isBlock() ) { ?>
	<a href="<?php echo $target->getPermalink();?>"><?php echo $target->getName();?></a>
	<?php } else { ?>
	<?php echo $target->getName();?>
	<?php } ?>
<?php } ?>
