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
<form name="adminForm" id="adminForm" class="privacyForm" method="post" enctype="multipart/form-data">
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<h3><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_INSTALL_SCAN' );?></h3>
				<p><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_INSTALL_SCAN_DESC' );?></p>
			</div>

			<div class="panel-body">
				<table class="table table-striped table-noborder">
					<tr>
						<td>
							<?php echo JPATH_ROOT;?>/administrator/components/com_components/privacy/
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JPATH_ROOT;?>/media/com_easysocial/apps/users/appname/privacy/
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JPATH_ROOT;?>/media/com_easysocial/apps/fields/fieldname/privacy/
						</td>
					</tr>
				</table>

				<a href="javascript:void(0);" class="btn btn-small btn-es-primary scanRules"><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_INSTALL_SCAN_START_SCAN' );?></a>

				<div class="mt-20 small">
					<span class="label label-info small"><?php echo JText::_( 'COM_EASYSOCIAL_FOOTPRINT_NOTE' );?>:</span> <?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_INSTALL_SCAN_FOOTPRINT' );?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_DISCOVER_RESULT' );?></b>
			</div>

			<div class="panel-body">
				<div class="es-progress-wrap">
					<div class="discoverProgress" stlye="display: none;">
						<div style="width: 0%;text-align:left;padding-left: 5px;" class="bar"></div>
						<div class="progress-result"></div>
					</div>
				</div>

				<a href="javascript:void(0);" class="viewLog btn btn-es-inverse btn-medium" style="display: none;">
					<?php echo JText::_( 'COM_EASYSOCIAL_VIEW_LOGS_BUTTON' );?>
				</a>

				<ul class="scannedResult es-scanned-result list-unstyled">
					<li class="empty">
						<?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_NO_ITEMS_DISCOVERED_YET' ); ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="privacy" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' );?>

</form>
