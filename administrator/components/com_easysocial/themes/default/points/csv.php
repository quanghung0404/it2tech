<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data">
<div class="row">

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_INSTALL_UPLOAD_CSV' );?></b>
				<p><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_INSTALL_UPLOAD_CSV_DESC' );?> <?php echo JText::_( 'COM_EASYSOCIAL_POINTS_INSTALL_UPLOAD_CSV_FORMAT_DESC' );?></p>
			</div>

			<div class="panel-body">
				<div class="mb-20">
					<code>"USER_ID"</code> , <code>"POINTS"</code> , <code>"CUSTOM_MESSAGE"</code>
				</div>

				<div>
					<ul class="list-unstyled">
						<li>
							<code>USER_ID</code> - <?php echo JText::_( 'COM_EASYSOCIAL_POINTS_CSV_USER_ID_DESC' ); ?>
						</li>
						<li class="mt-5">
							<code>POINTS</code> - <?php echo JText::_( 'COM_EASYSOCIAL_POINTS_CSV_POINTS_DESC' ); ?>
						</li>
						<li class="mt-5">
							<code>CUSTOM_MESSAGE</code> (<?php echo JText::_( 'COM_EASYSOCIAL_OPTIONAL' );?>) - <?php echo JText::_( 'COM_EASYSOCIAL_POINTS_CSV_CUSTOM_MSG' ); ?>
						</li>
					</ul>
				</div>

				<div>
					<input type="file" name="package" id="package" class="input" style="width:265px;" data-uniform />
					<button class="btn btn-small btn-es-primary installUpload"><?php echo JText::_( 'Upload CSV File' );?> &raquo;</button>
				</div>
			</div>
		</div>
	</div>

</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="points" />
<input type="hidden" name="task" value="massAssign" />
<?php echo JHTML::_( 'form.token' );?>

</form>
