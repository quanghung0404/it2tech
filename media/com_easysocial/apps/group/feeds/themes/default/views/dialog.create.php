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
	<width>500</width>
	<height>250</height>
	<selectors type="json">
	{
		"{saveButton}"		: "[data-save-button]",
		"{cancelButton}"	: "[data-cancel-button]",
		"{title}": "[data-feeds-form-title]",
		"{url}": "[data-feeds-form-url]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('APP_FEEDS_DIALOG_CREATE_TITLE'); ?></title>
	<content>
		<p class="mb-20"><?php echo JText::_('APP_GROUP_FEEDS_DIALOG_CREATE_DESC'); ?></p>

		<div data-feeds-form-notice class="hide"></div>

		<label for="feed-title">
			<?php echo JText::_('APP_FEEDS_DIALOG_FORM_CREATE_TITLE'); ?>:
		</label>
		<input type="text" name="title" value="" id="feed-title" class="input-sm form-control"  placeholder="<?php echo JText::_('APP_FEEDS_DIALOG_FORM_CREATE_TITLE');?>" data-feeds-form-title />

		<div class="mt-10">
			<label for="feed-url">
				<?php echo JText::_('APP_FEEDS_DIALOG_FORM_CREATE_URL'); ?>:
			</label>
			<input type="text" name="title" value="" id="feed-url" class="input-sm form-control" placeholder="<?php echo JText::_('APP_FEEDS_DIALOG_FORM_CREATE_URL_PLACEHOLDER');?>" data-feeds-form-url />
		</div>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></button>
		<button data-save-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_( 'APP_FEEDS_CREATE_BUTTON' ); ?></button>
	</buttons>
</dialog>
