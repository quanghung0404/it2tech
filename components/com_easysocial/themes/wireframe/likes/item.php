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
<div class="es-likes-wrap<?php echo ( empty( $count) ) ? ' hide' : ''; ?>"
	 data-likes-content
	 data-id="<?php echo $uid; ?>"
	 data-type="<?php echo $element; ?>"
	 data-group="<?php echo $group; ?>"
	 data-count="<?php echo $count; ?>"
	 data-verb="<?php echo $verb;?>"
	 <?php echo 'data-likes-' . $element . '-' . $group . '-' . $uid; ?>
>
<?php if( $count > 0 ) { ?>
	<?php echo $text; ?>
<?php } ?>
</div>
