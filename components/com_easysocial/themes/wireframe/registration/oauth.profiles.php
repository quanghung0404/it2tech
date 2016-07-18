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
<li>
	<div class="list-profile">
		<div class="pa-15">
			<div class="media">
				<div class="media-object pull-left">
					<?php if( $this->template->get( 'registration_profile_avatar' ) ){ ?>
					<span class="es-avatar es-avatar-md">
						<img class="" src="<?php echo $profile->getAvatar( SOCIAL_AVATAR_LARGE );?>" title="<?php echo $this->html( 'string.escape' , $profile->getTitle() );?>" />
					</span>
					<?php } ?>
				</div>
				<div class="media-body">

					<h3 class="list-profile-title">
						<?php echo $profile->get( 'title' );?>
					</h3>

					<?php if( $this->template->get( 'registration_profile_desc' ) ){ ?>
					<div class="list-profile-description">
						<?php echo $profile->get( 'description' );?>
					</div>
					<?php } ?>

					<?php if( $this->template->get( 'registration_profile_type' ) && ( $profile->getRegistrationType() == 'approvals' || $profile->getRegistrationType() == 'verify') ){ ?>
						<div class="small mt-10">* <?php echo $profile->getRegistrationType( SOCIAL_TRANSLATE_REGISTRATION ); ?></div>
					<?php } ?>

					<?php if( $profile->getMembersCount() && $this->template->get( 'registration_profile_users' ) ) { ?>
						<hr />
						<div class="list-profile-type-peep small mt-5 mb-5">
							<?php echo JText::sprintf( 'COM_EASYSOCIAL_REGISTRATIONS_OTHER_PROFILE_MEMBERS' , $profile->getMembersCount() );?>
						</div>

						<?php if( $profile->users ){ ?>
						<ul class="fd-reset-list list-inline profile-users">
							<?php foreach( $profile->users as $user ){ ?>
							<li data-es-provide="tooltip" data-original-title="<?php echo $this->html( 'string.escape' , $user->getName() );?>" style="float: left;">
								<a href="<?php echo $user->getPermalink();?>" class="es-avatar pull-left mr-10">
									<img width="24" height="24" class="img-polaroid" src="<?php echo $user->getAvatar( SOCIAL_AVATAR_SMALL );?>" title="<?php echo $this->html( 'string.escape' , $user->getName() );?>" />
								</a>
							</li>
							<?php } ?>
						</ul>
						<?php } ?>

					<?php } ?>

				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-es-primary btn-medium" data-oauth-profile-submit data-id="<?php echo $profile->id;?>"><?php echo JText::_( 'COM_EASYSOCIAL_SELECT_THIS_PROFILE_BUTTON' ); ?></button>
		</div>
	</div>
</li>


