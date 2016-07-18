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
<form name="adminForm" id="adminForm" method="post">

	<div class="form-api-key">
		<h3>
			<i class="icon-jar jar-warning"></i>
			<span><?php echo JText::_( 'COM_EASYSOCIAL_SETTINGS_API_KEY_REQUIRED' );?></span>
		</h3>
		<p class="form-info">
			<?php echo JText::_( 'COM_EASYSOCIAL_SETTINGS_API_KEY_REQUIRED_DESC' );?>
		</p>

		<div class="key-input">
			<div class="input-group">
				<input type="text" class="form-control" name="key" value="" />
				<div class="input-group-btn">
					<button class="btn btn-es-primary"><?php echo JText::_( 'COM_EASYSOCIAL_SAVE_API_KEY_BUTTON' ); ?></button>
				</div>
			</div>

			<div class="obtain-key">
				<i class="icon-es-help mr-5"></i> <a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_SETTINGS_OBTAINING_API_KEY' ); ?></a>
			</div>
		</div>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="controller" value="settings" />
	<input type="hidden" name="task" value="savekey" />
	<input type="hidden" name="return" value="<?php echo $return;?>" />
</form>
