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
<div class="fd-explorer"
	data-fd-explorer="<?php echo $uuid;?>"
	data-uid="<?php echo $uid; ?>"
	data-type="<?php echo $type; ?>"
	data-url="site/controllers/explorer/hook"
	data-controller-name="<?php echo isset($options['controllerName']) ? $options['controllerName'] : 'groups';?>"
	>

	<div class="fd-explorer-header">
		<div class="fd-explorer-sidebar-action pull-left">
			<button class="btn btn-es btn-sm" data-fd-explorer-button="addFolder"><i class="fa fa-plus"></i> <?php echo JText::_( 'COM_EASYSOCIAL_EXPLORER_ADD_FOLDER' );?></button>
		</div>
		<div class="fd-explorer-browser-action">

			<?php if( !isset( $options[ 'allowUpload' ] ) || $options[ 'allowUpload' ] ){ ?>
				<button class="btn btn-es btn-sm fd-explorer-upload-button" data-plupload-upload-button>
					<i class="fa fa-upload"></i> <?php echo JText::_( 'COM_EASYSOCIAL_EXPLORER_UPLOAD' );?>
				</button>

				<?php if( isset( $options[ 'uploadLimit' ] ) ){ ?>
					<span class="upload-limit">
						<?php echo JText::sprintf( 'COM_EASYSOCIAL_EXPLORER_UPLOAD_LIMIT' , $options[ 'uploadLimit' ] ); ?>
					</span>
				<?php } ?>
			<?php } ?>

			<button class="btn btn-danger btn-sm pull-right close-button"><i class="fa fa-remove"></i></button>
			<button class="btn btn-success btn-sm pull-right insert-button mr-5" data-fd-explorer-button="useFile">
				<i class="fa fa-check"></i> <?php echo JText::_( 'COM_EASYSOCIAL_EXPLORER_INSERT' );?>
			</button>
			<button class="btn btn-primary btn-sm pull-right preview-button" data-fd-explorer-button="previewFile">
				<i class="fa fa-eye"></i> <?php echo JText::_( 'COM_EASYSOCIAL_EXPLORER_PREVIEW' );?>
			</button>
			<i class="es-loading-indicator fd-small"></i>
		</div>
	</div>
	<div class="fd-explorer-funky form-group" style="display: none;">
		<div class="checkbox">
			<label>
			  <input type="checkbox" name="mock_error"> Mock error <small>(error=1 in post request)</small>
			</label>
		</div>
		<div class="checkbox">
			<label>
			  <input type="checkbox" name="disable_validation"> Disable client-side validation <small>(let server-side return error)</small>
			</label>
		</div>
		<hr class="funkybar"/>
		<div class="alert alert-info" data-alertlog>Log message will show here.</div>
		<hr class="funkybar"/>
		<small class="service-state state-idle">
			<span class="idle">Idle</span>
			<span class="working"><i class="es-loading-indicator fd-small"></i>Working...</span>
		</small>
	</div>
	<div class="fd-explorer-titlebar">
		<div class="fd-explorer-titlebar-side">
			<i class="fa fa-folder-2"></i> <?php echo JText::_( 'COM_EASYSOCIAL_EXPLORER_FOLDERS' );?>
		</div>

		<div class="fd-explorer-titlebar-content">
			<label class="checkbox-inline">
				<input type="checkbox" data-fd-explorer-select-all />
			</label>
			<a href="javascript:void(0);" data-fd-explorer-button="removeFile"><?php echo JText::_( 'COM_EASYSOCIAL_EXPLORER_DELETE_SELECTED' );?></a>
		</div>
	</div>
	<div class="fd-explorer-content">
		<div class="fd-explorer-sidebar">
			<div class="fd-explorer-folder-group">
			</div>
		</div>
		<div class="fd-explorer-browser">
			<div class="fd-explorer-viewport"></div>
		</div>
	</div>
</div>
