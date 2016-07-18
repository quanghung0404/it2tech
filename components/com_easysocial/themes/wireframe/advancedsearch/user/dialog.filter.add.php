<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
	<width>450</width>
	<height>200</height>
	<selectors type="json">
	{
		"{saveButton}"		: "[data-save-button]",
		"{cancelButton}"	: "[data-cancel-button]",
		"{inputTitle}"		: "[data-filter-name]",
		"{inputSitewide}"	: "[data-filter-sitewide]",
		"{inputWarning}" 	: "[filter-form-notice]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_FILTER_ADD_DIALOG_TITLE' ); ?></title>
	<content>
		<p><?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_FILTER_ADD_CONFIRMATION' ); ?></p>


		<div class="alert" filter-form-notice style="display:none;"><?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_FILTER_WARNING_TITLE_EMPTY_SHORT' ); ?></div>

		<div class="control-group mt-15">
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_FILTER_TITLE' ); ?>:</label>
			<div class="controls">
				<input type="text" name="title" value="" data-filter-name class="input-sm form-control" />

				<?php if ($this->my->isSiteAdmin()) { ?>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="sitewide" value="1" data-filter-sitewide /> <?php echo JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_SAVE_AS_SITEWIDE_FILTER'); ?>
                        </label>
                    </div>

				<?php } ?>
			</div>

		</div>

	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-save-button type="button" class="btn btn-es-primary"><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON'); ?></button>
	</buttons>
</dialog>
