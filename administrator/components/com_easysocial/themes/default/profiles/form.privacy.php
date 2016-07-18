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

$curView = JRequest::getVar( 'view', '' );
?>
<div class="row" data-edit-privacy>
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_PROFILE_PRIVACY_PANEL_TITLE' );?></b>
			</div>

			<div class="panel-body">

				<?php if(empty( $privacy) ) { ?>
					<div class="form-group">
						<label class="col-md-5">
							<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_NOT_FOUND'); ?>
						</label>
					</div>
				<?php } else { ?>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" value="1" name="privacyReset"/> <?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_RESET_ALL_USER_DESCRIPTION' ); ?>
							</label>
						</div>
					</div>

					<?php

						$index = 0;
						foreach( $privacy->getData() as $key => $groups) {
					?>

					<div class="form-group">
						<div class="col-md-5">
							<h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_' . strtoupper($key) ); ?></h4>
						</div>
					</div>

						<?php


						foreach($groups as $item) {

							$gKey  =  strtoupper($key);
							$rule  =  str_replace( '.', '_', $item->rule);
							$rule  =  strtoupper($rule);
							$ruleLangKeys = 'COM_EASYSOCIAL_PROFILES_' . strtoupper($gKey) . '_' . strtoupper($rule);
							$hasCustom = false;
							$isCustom  = false;
							$customIds = '';
						?>

						<div class="form-group privacyItem" data-privacy-item>
							<label class="col-md-6">
								<?php echo JText::_( $ruleLangKeys ); ?>
								<i class="fa fa-question-circle pull-right"
									<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_' . $gKey ) , JText::_( $ruleLangKeys ) , 'bottom' ); ?>
								></i>
							</label>
							<div class="col-md-6">
								<select class="form-control input-sm privacySelection" name="privacy[<?php echo $gKey;?>][<?php echo $rule;?>]" data-privacy-select >
									<?php foreach( $item->options as $option => $value) {

										// profiles page shouldnt allow to see this option.
										if( $option == 'custom' && $curView == 'profiles' )
											continue;

										$hasCustom = ( $option == 'custom' && $value ) ? true : false;
									?>
										<option value="<?php echo $option?>" <?php echo ($value) ? 'selected="selected"': ''?> ><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_OPTION_' . strtoupper($option)); ?></option>
									<?php } ?>
								</select>

								<div data-privacy-custom-form
									class="dropdown-menu dropdown-arrow-topleft privacy-custom-menu"
									<?php if( !$hasCustom ) { ?>style="display: none;"<?php } ?>
								>
									<div class="fd-small mb-10"><?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_NAME'); ?></div>
									<div class="textboxlist" data-textfield>

										<?php
											if( $hasCustom )
											{
												foreach( $item->custom as $friend )
												{
													if( $customIds )
													{
														$customIds = $customIds . ',' . $friend->user_id;
													}
													else
													{
														$customIds = $friend->user_id;
													}

													$friend = FD::user( $friend->user_id );
										?>
											<div class="textboxlist-item" data-id="<?php echo $friend->id; ?>" data-title="<?php echo $friend->getName(); ?>" data-textboxlist-item>
												<span class="textboxlist-itemContent" data-textboxlist-itemContent><?php echo $friend->getName(); ?><input type="hidden" name="items" value="<?php echo $friend->id; ?>" /></span>
												<a class="textboxlist-itemRemoveButton" href="javascript: void(0);" data-textboxlist-itemRemoveButton></a>
											</div>
										<?php
												}

											}
										?>

										<input type="text" class="textboxlist-textField" data-textboxlist-textField placeholder="<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_ENTER_NAME'); ?>" autocomplete="off" />
									</div>
								</div>

								<input type="hidden" name="privacyID[<?php echo $gKey;?>][<?php echo $rule;?>]" value="<?php echo $item->id . '_' . $item->mapid; ?>" />
								<input type="hidden" data-hidden-custom name="privacyCustom[<?php echo $gKey;?>][<?php echo $rule; ?>]" value="<?php echo $customIds; ?>" />
							</div>
						</div>
						<?php } ?>

					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
