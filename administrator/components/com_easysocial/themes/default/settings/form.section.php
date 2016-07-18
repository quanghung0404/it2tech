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
<div class="panel">
	<?php echo $header; ?>

	<div class="panel-body">
		<?php if (is_array($section)) { ?>
			<?php foreach ($section as $data) { ?>
				<?php if (is_array($data)) { ?>
					<?php echo call_user_func_array(array($settings, 'renderSetting'), $data); ?>
				<?php } ?>

				<?php if (is_string($data)) { ?>
					<?php echo $data;?>
				<?php } ?>
			<?php } ?>
		<?php } ?>

		<?php if (is_string($section)) { ?>
			<?php echo $section;?>
		<?php } ?>
	</div>
</div>
