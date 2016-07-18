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
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{purgeButton}"		: "[data-purge-button]",
		"{cancelButton}"	: "[data-cancel-button]",
		"{form}"			: "[data-purge-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function()
		{
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_( 'COM_EASYSOCIAL_CONFIRM_PURGE_CACHE_DIALOG_TITLE' ); ?></title>
	<content>
		<form action="index.php" method="post" data-purge-form>
			<p><?php echo JText::_( 'COM_EASYSOCIAL_CONFIRM_PURGE_CACHE_CONFIRMATION' ); ?></p>

			<div class="checkbox">
				<label for="stylesheet-cache fd-small">
					<input type="checkbox" checked="checked" id="stylesheet-cache" value="1" name="stylesheet-cache" /> <?php echo JText::_( 'COM_EASYSOCIAL_PURGE_STYLESHEET_CACHE' );?>
				</label>
			</div>
			<div class="checkbox">
				<label for="script-cache fd-small">
					<input type="checkbox" checked="checked" id="script-cache" value="1" name="script-cache" /> <?php echo JText::_( 'COM_EASYSOCIAL_PURGE_SCRIPT_CONFIGURATION' );?>
				</label>
			</div>

			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="controller" value="easysocial" />
			<input type="hidden" name="task" value="clearCache" />
			<?php echo $this->html( 'form.token' ); ?>
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-sm btn-es"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-purge-button type="button" class="btn btn-sm btn-es-primary"><?php echo JText::_( 'COM_EASYSOCIAL_PURGE_CACHE_BUTTON' ); ?></button>
	</buttons>
</dialog>
