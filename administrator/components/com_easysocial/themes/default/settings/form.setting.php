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
<div class="form-group"<?php echo $rowAttributes; ?>>
	<label class="col-md-5" for="page_title">
		<?php echo $label; ?>
		<?php if( !empty( $help ) ) { ?>
		<i class="fa fa-question-circle pull-right"
			<?php echo $help; ?>
		></i>
		<?php } ?>
	</label>
	<div class="col-md-7">
		<?php echo $field; ?>

		<?php if( !empty( $unit ) ) { ?>
		<strong><?php echo $unit; ?></strong>
		<?php } ?>

		<?php if( !empty( $info ) ) { ?>
		<div class="small"><?php echo $info; ?></div>
		<?php } ?>

		<?php if( isset( $custom ) && !empty( $custom ) ) { ?>
		<div class="small">
			<?php echo $custom; ?>
		</div>
		<?php } ?>
	</div>
</div>
