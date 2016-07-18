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
<div class="row">
    <div class="col-lg-7">
		<div class="panel">
			<ul class="panel-head panel-tabs list-unstyled clearfix">
				<?php $i = 0; ?>

				<?php foreach ($steps as $step) { ?>
					<li class="tab-item<?php echo $i == 0 ? ' active' : '';?>" data-stepnav data-for="<?php echo $step->id; ?>">
						<a href="#step-<?php echo $step->id;?>" data-bs-toggle="tab"><?php echo $step->get( 'title' );?></a>
					</li>
					<?php $i++; ?>
				<?php } ?>

				<li class="tab-item" data-stepnav data-for="setting">
					<a href="#setting" data-bs-toggle="tab">
						<?php echo JText::_('COM_EASYSOCIAL_USERS_ACCOUNT_SETTING');?>
					</a>
				</li>
			</ul>

			<div class="es-user-profile-content tab-content">
				<?php $x = 0;?>
				
				<?php foreach ($steps as $step) { ?>
				
					<div id="step-<?php echo $step->id;?>" class="tab-pane<?php echo $x == 0 ? ' active' : '';?>" data-profile-adminedit-fields-content data-stepcontent data-for="<?php echo $step->id; ?>">
					<?php foreach( $step->fields as $field ) { ?>
						<?php if( !empty( $field->output ) ) { ?>
						<div data-profile-adminedit-fields-item data-element="<?php echo $field->element; ?>" data-fieldname="<?php echo SOCIAL_FIELDS_PREFIX . $field->id; ?>" data-id="<?php echo $field->id; ?>" data-required="<?php echo $field->required; ?>">
							<?php echo $field->output; ?>
						</div>
						<?php } ?>

						<?php if (!$field->getApp()->id) { ?>
						<div class="alert alert-danger"><?php echo JText::_('COM_EASYSOCIAL_FIELDS_INVALID_APP'); ?></div>
						<?php } ?>
					<?php } ?>

					</div>
					<?php $x++; ?>
				<?php } ?>

				<div id="setting" class="tab-pane" data-profile-adminedit-fields-content data-stepcontent data-for="setting">
					<legend><?php echo JText::_('COM_EASYSOCIAL_USERS_ACCOUNT_SETTING');?></legend>
					<div class="form-group ">
						<label for="require_reset" class="col-sm-3 control-label">
							<?php echo JText::_('COM_EASYSOCIAL_USERS_ACCOUNT_REQUIRE_RESET'); ?>:
							<i class="icon-es-help pull-right"
								<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_USERS_ACCOUNT_REQUIRE_RESET' ) , JText::_( 'COM_EASYSOCIAL_USERS_ACCOUNT_REQUIRE_RESET_HELP' ) , 'bottom' ); ?>
							></i>
						</label>

						<div class="col-sm-8 data">
							<?php echo $this->html( 'grid.boolean' , 'require_reset' , isset($user->require_reset) ? $user->require_reset : '0' , 'require_reset' ); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>