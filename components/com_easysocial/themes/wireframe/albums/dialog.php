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
	<width>800</width>
	<height>500</height>
	<selectors type="json">
	{
		"{selectButton}"  : "[data-select-button]",
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{selectButton} click": function() {

			var selectedItems =
				this.find("[data-album-item]")
					.controller("EasySocial.Controllers.Albums.Item")
					.getSelectedItems();

			this.trigger("photoSelected", [selectedItems]);
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_DIALOG_TITLE'); ?></title>
	<content>
		<?php echo $this->includeTemplate("site/albums/dialog.browser"); ?>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-select-button type="button" class="btn btn-es-success btn-sm"><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_SELECT'); ?></button>
	</buttons>
</dialog>
