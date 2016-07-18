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
<form name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data">
<div class="row">

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_FORM_GENERAL' );?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_FORM_DEFAULT' );?>
						<i data-placement="bottom" data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_FORM_DEFAULT' , true );?>" data-content="<?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_FORM_DEFAULT_DESC' , true ); ?>" data-es-provide="popover" class="fa fa-question-circle pull-right" data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<select class="input-full" value="<?php echo $privacy->value;?>" name="value">
						<?php
							$options = FD::json()->decode( $privacy->options );

							foreach( $options->options as $option )
							{
								//$value 		= FD::call( 'Privacy' , 'toValue' , $option );
								$value 		= FD::privacy()->toValue( $option );
								$isChecked 	= ( $privacy->value == $value ) ? ' selected="selected"' : '';
								$label     	= JText::_( 'COM_EASYSOCIAL_PRIVACY_OPTION_' . strtoupper( $option ) );
							?>
							<option value="<?php echo $value; ?>"<?php echo $isChecked; ?>><?php echo $label; ?></option>
						<?php
							}
						?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="privacy" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $privacy->id; ?>" />
<?php echo JHTML::_( 'form.token' );?>

</form>
