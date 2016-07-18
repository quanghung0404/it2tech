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
<dialog>
	<width>300</width>
	<height>100</height>
	<title><?php echo JText::_('COM_EASYSOCIAL_REPOST_FORM_DIALOG_TITLE'); ?></title>
	<content>
		<div class="es-repost-form">
		<?php echo $message; ?>
		</div>
	</content>
	<buttons>
		<button type="button" class="dialog-closeButton btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CLOSE_BUTTON'); ?></button>
	</buttons>
</dialog>
