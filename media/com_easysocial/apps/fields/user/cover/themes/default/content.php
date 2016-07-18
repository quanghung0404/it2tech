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
<div data-field-cover class="data-field-cover">
	<ul class="input-vertical list-unstyled">
		<li>
			<div class="cover-image-wrap">
				<div data-field-cover-image class="cover-image cover-move" style="background-image: url(<?php echo $value; ?>);background-position: <?php echo !empty( $position ) ? $position : ''; ?>;"></div>
				<div class="cover-remove">
					<a href="javascript:void(0);" data-field-cover-remove-button <?php if( !$hasCover ) { ?>style="display: none;"<?php } ?>>×</a>
				</div>
				<i class="loading-indicator fd-small" data-field-cover-loader style="display: none;"></i>
			</div>

			<div class="mb-10" data-field-cover-revert style="display: none;">
				<a href="javascript:void(0);" data-field-cover-revert-button><?php echo JText::_( 'PLG_FIELDS_COVER_REVERT_COVER' ); ?></a>
			</div>

			<div class="mb-10" data-field-cover-note <?php if( empty( $value ) ) { ?>style="display: none;"<?php } ?>><strong><?php echo JText::_('COM_EASYSOCIAL_NOTE');?>:</strong> <?php echo JText::_( 'PLG_FIELDS_COVER_REPOSITION_COVER' ); ?></div>


			<div class="input-group input-group-sm">
				<span class="input-group-btn">
					<span class="btn btn-es-primary btn-file">
						<?php echo JText::_('FIELDS_USER_COVER_BROWSE_FILE'); ?>&hellip; <input type="file" id="<?php echo $inputName; ?>" name="<?php echo $inputName; ?>[file]" data-field-cover-file />
					</span>
				</span>
				<input class="form-control" type="text" readonly />
			</div>


			<div class="text-danger" data-field-cover-error></div>
		</li>
	</ul>

	<input type="hidden" id="<?php echo $inputName; ?>" name="<?php echo $inputName; ?>[data]" data-field-cover-data <?php if( !empty( $coverData ) ) { ?>value="<?php echo $coverData; ?>"<?php } ?> />
	<input type="hidden" id="<?php echo $inputName; ?>" name="<?php echo $inputName; ?>[position]" data-field-cover-position <?php if( !empty( $coverPosition ) ) { ?>value="<?php echo $coverPosition; ?>"<?php } ?> />
</div>
