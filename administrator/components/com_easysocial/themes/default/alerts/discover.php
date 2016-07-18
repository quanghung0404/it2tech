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
<form name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data" data-alerts-discovery>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_ALERTS_INSTALL_SCAN' );?></b>
				<p><?php echo JText::_( 'COM_EASYSOCIAL_ALERTS_INSTALL_SCAN_DESC' );?></p>
			</div>

			<div class="panel-head">
				<table class="table table-striped table-noborder">
					<tr>
						<td>
							<?php echo JPATH_ROOT;?>/administrator/components/
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JPATH_ROOT;?>/media/com_easysocial/apps/users/
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JPATH_ROOT;?>/media/com_easysocial/apps/fields/
						</td>
					</tr>
				</table>

				<div class="mt-20 fd-small">
					<span class="label label-danger"><?php echo JText::_( 'COM_EASYSOCIAL_FOOTPRINT_NOTE' );?>:</span> <?php echo JText::_( 'COM_EASYSOCIAL_DISCOVERY_FOOTPRINT' );?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_DISCOVERY_RESULT' );?></b>
			</div>

			<div class="panel-body">
				<div class="es-progress-wrap">
					<div stlye="display: none;" data-alerts-discovery-progress class="discoverProgress">
						<div style="width: 0%;text-align:left;padding-left: 5px;" class="bar"></div>
						<div class="progress-result"></div>
					</div>
				</div>

				<div class="discovery-log">
					<table class="table table-striped table-noborder" data-alerts-discovery-result>
						<tr>
							<td>
								<?php echo JText::_( 'COM_EASYSOCIAL_NO_ITEMS_DISCOVERED_YET' ); ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="points" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' );?>

</form>
