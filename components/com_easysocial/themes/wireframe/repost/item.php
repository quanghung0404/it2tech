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
<div class="es-repost-wrap<?php echo ( empty( $count) ) ? ' hide' : ''; ?>"
	 data-repost-content
	 data-id="<?php echo $uid; ?>"
	 data-element="<?php echo $element; ?>.<?php echo $group;?>"
	 data-count="<?php echo $count; ?>"
	 <?php echo 'data-repost-' . $element . '-' . $group . '-' . $uid; ?>>

	<a href="javascript:void(0);"
	   data-popbox="module://easysocial/repost/authors"
	   data-popbox-toggle="click">
	   <i class="icon-es-shared"></i> <span class="repost-counter"><?php echo $text; ?></span>
	</a>
</div>
