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
<?php if( $this->config->get( 'registrations.steps.progress' ) ){ ?>
<div class="navbar es-stepbar">
	<div class="navbar-inner">
		<div class="navbar-collapse collapse">
			<div class="media">

				<div class="media-object pull-left">
					<ul class="fd-nav">
						<!-- Select a profile -->
						<li class="stepItem<?php echo $currentStep == SOCIAL_REGISTER_SELECTPROFILE_STEP ? ' active' : '';?><?php echo $currentStep > SOCIAL_REGISTER_SELECTPROFILE_STEP ||  $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active past' : '';?>"
							data-es-provide="popover"
                            data-placement="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'left' : 'right';?>"
							data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_SELECT_A_PROFILE' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_SELECT_A_PROFILE_DESC' , true );?>">

							<a href="<?php echo FRoute::registration();?>">
								<i class="fa fa-check"></i>
								<span class="step-number">0</span>
							</a>
						</li>
						<!-- Progress -->
						<?php $counter = 1; ?>
						<?php foreach( $steps as $step ){ ?>
						<?php
							$customClass	= $step->sequence == $currentStep || $currentStep > $step->sequence || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active' : '';
							$customClass 	.= $step->sequence < $currentStep || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? $customClass . ' past' : '';

							if( $registration->hasStepAccess( $step->sequence ) )
							{
								// $link 		= $step->sequence == $currentStep ? 'javascript:void(0);' : FRoute::registration( array( 'layout' => 'steps' , 'step' => $step->sequence ) );
								$link 		= $step->sequence == $currentStep ? 'javascript:void(0);' : FRoute::registration( array( 'layout' => 'steps' , 'step' => $counter ) );
							}
							else
							{
								$link 		= 'javascript:void(0);';
							}
						?>
						<li class="divider-vertical<?php echo $customClass;?>"></li>
						<li class="stepItem<?php echo $customClass;?>"
							data-original-title="<?php echo JText::_( $step->title , true );?>"
							data-content="<?php echo JText::_( $step->description , true );?>"
                            data-placement="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'left' : 'right';?>"
							data-es-provide="popover">

							<a href="<?php echo $link;?>">
								<i class="fa fa-check"></i>
								<span class="step-number"><?php echo $counter; ?></span>
							</a>
						</li>
						<?php $counter++; ?>
						<?php } ?>

						<!-- Complete step -->
						<li class="divider-vertical<?php echo $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active past' : '';?>"></li>
						<li class="stepItem last<?php echo $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active past' : '';?>"
							data-es-provide="popover"
                            data-placement="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'left' : 'right';?>"
							data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_REGISTRATION_COMPLETE' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_REGISTRATION_COMPLETE_DESC' , true );?>"
							>

							<a href="javascript:void(0);">
								<i class="fa fa-flag"></i>
							</a>
						</li>
					</ul>
				</div>
				<div class="media-body">
					<div class="divider-vertical-last"></div>
				</div>

			</div>
		</div>
	</div>
</div>
<?php } ?>
