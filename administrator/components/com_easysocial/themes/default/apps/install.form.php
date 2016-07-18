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
<form name="installerForm" class="installerForm" method="post" enctype="multipart/form-data">
<div class="row">
	<div class="col-md-6">

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_UPLOAD_PACKAGE');?></b>
				<p><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_UPLOAD_PACKAGE_INFO' ); ?></p>
			</div>

			<div class="panel-body">
				<input type="file" name="package" id="package" class="input" style="width:265px;" data-uniform />
				<button class="btn btn-es-primary btn-small installUpload"><?php echo JText::_( 'COM_EASYSOCIAL_UPLOAD_AND_INSTALL_BUTTON' );?> &raquo;</button>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<h3><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_DIRECTORY_PACKAGE');?></h3>
				<p><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_DIRECTORY_PACKAGE_INFO' ); ?></p>
			</div>

			<div class="panel-body">
				<div class="input-group" style="padding-right: 160px;">
					<input type="text" name="package-directory" id="package-directory" value="<?php echo $temporaryPath;?>" class="form-control" />
					<div class="input-group-btn">
						<button class="btn btn-es-primary installDirectory"><?php echo JText::_( 'COM_EASYSOCIAL_UPLOAD_AND_INSTALL_BUTTON' );?> &raquo;</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">	
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_DIRECTORY_PERMISSIONS' );?></b>
			</div>

			<div class="panel-body">
				<table class="table table-striped table-noborder">
					<thead>
						<th><?php echo JText::_( 'COM_EASYSOCIAL_APPS_DIRECTORY' );?></th>
						<th><?php echo JText::_( 'COM_EASYSOCIAL_APPS_PERMISSIONS' );?></th>
					</thead>
					<tbody>
						<?php foreach( $directories as $directory ){ ?>
						<tr>
							<td>
								<span style="word-break:break-all">
									<?php echo $directory->path; ?>
								</span>
							</td>
							<td>
								<?php if( $directory->writable ){ ?>
									<span class="text-success" data-es-provide="tooltip" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_WRITABLE_DESC' , true );?>">
										<?php echo JText::_( 'COM_EASYSOCIAL_APPS_DIRECTORY_WRITABLE' ); ?>
									</span>
								<?php } else { ?>
									<span class="text-danger" data-es-provide="tooltip" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_UNWRITABLE_DESC' , true );?>">
										<?php echo JText::_( 'COM_EASYSOCIAL_APPS_DIRECTORY_UNWRITABLE' ); ?>
									</span>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="apps" />
<input type="hidden" name="task" value="install" />
</form>
