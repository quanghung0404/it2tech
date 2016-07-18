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
<div class="fd-explorer-file" data-id="<?php echo $file->id; ?>" data-preview-uri="<?php echo $file->data->previewUri; ?>">
	<?php if ($file->canDelete) { ?>
	<a href="javascript: void(0);" class="btn btn-danger btn-sm btn-file-remove" data-fd-explorer-delete-button><?php echo JText::_('COM_EASYSOCIAL_EXPLORER_DELETE_FILE'); ?></a>
	<?php } ?>
	<div class="pull-left">
		<label class="checkbox-inline">
			<input type="checkbox" value="<?php echo $file->id; ?>" data-fd-explorer-select>
		</label>
	</div>
	<div class="media mt-5 ml-10">
		<div class="media-object pull-left">
			<i class="<?php echo $file->data->icon; ?>"></i>
		</div>
		<div class="media-body">
			<div class="file-title"><?php echo $file->name; ?></div>
			<div class="file-meta"><?php echo FD::date( $file->data->created )->format( JText::_( 'DATE_FORMAT_LC1' ), true ); ?></div>
		</div>
	</div>
</div>
