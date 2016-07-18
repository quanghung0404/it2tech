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
	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_GENERAL');?></b>
				<p class="panel-info"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_GENERAL_INFO');?></p>
			</div>

			<div class="panel-body">
				<div class="form-group" data-profile-avatar data-hasavatar="<?php echo $profile->hasAvatar(); ?>" data-defaultavatar="<?php echo $profile->getDefaultAvatar(); ?>">
					<label class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_AVATAR' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_AVATAR' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_AVATAR_TIPS_DESC' ) , 'bottom' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_TITLE_PLACEHOLDER' ) ); ?>
						></i>
					</label>

					<div class="col-md-8">

						<?php if( $profile->id ){ ?>
						<div class="mb-20">
							<img src="<?php echo $profile->getAvatar();?>" class="es-avatar es-avatar-md es-avatar-border-sm" data-profile-avatar-image />
						</div>
						<?php } ?>

						<div>
							<input type="file" name="avatar" data-uniform data-profile-avatar-upload />
							<span data-profile-avatar-remove-wrap <?php if( !$profile->hasAvatar() ) { ?>style="display: none;"<?php } ?>> <?php echo JText::_( 'COM_EASYSOCIAL_OR' ); ?>
								<a href="javascript:void(0);" class="btn btn-sm btn-es-danger" data-profile-avatar-remove-button>
									<?php echo $profile->hasAvatar() ? JText::_('COM_EASYSOCIAL_PROFILES_FORM_REMOVE_AVATAR') : JText::_('COM_EASYSOCIAL_PROFILES_FORM_CLEAR_AVATAR'); ?>
								</a>
							</span>
						</div>
					</div>
				</div>

				<?php if (FD::get('multisites')->exists()) { ?>
				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_SITE_ID' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_SITE_ID' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_SITE_ID_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8"><?php echo FD::get('multisites')->getForm('site_id', $profile->site_id); ?></div>
				</div>
				<?php } ?>

				<div class="form-group">
					<label for="title" class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_TITLE' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_TITLE' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_TITLE_TIPS_DESC' ) , 'bottom' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_TITLE_PLACEHOLDER' ) ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<input type="text" name="title" id="title" class="form-control input-sm" value="<?php echo $profile->title;?>"/>
					</div>
				</div>

				<div class="form-group">
					<label for="title" class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_ALIAS_TITLE' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_ALIAS_TITLE' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_ALIAS_TIPS_DESC' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<input type="text" name="alias" id="alias" class="form-control input-sm" value="<?php echo $profile->alias;?>"/>
					</div>
				</div>

				<div class="form-group">
					<label for="description" class="col-md-4">
						<?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_DESCRIPTION');?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DESCRIPTION' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DESCRIPTION_TIPS_DESC' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<textarea name="description"
							id="description"
							class="form-control input-sm"
							data-profile-description
						><?php echo $profile->description;?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_PUBLISHING_STATUS');?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PUBLISHING_STATUS' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PUBLISHING_STATUS_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<?php echo $this->html( 'grid.boolean' , 'state' , $profile->state , 'state' ); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DEFAULT_PROFILE' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DEFAULT_PROFILE' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DEFAULT_PROFILE_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<?php echo $this->html( 'grid.boolean' , 'default' , $profile->default , 'default' ); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PROFILE_DELETION' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PROFILE_DELETION' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PROFILE_DELETION_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<?php echo $this->html( 'grid.boolean' , 'params[delete_account]' , $param->get( 'delete_account') , 'params[delete_account]' ); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PROFILE_REGISTRATION' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PROFILE_REGISTRATION' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_PROFILE_REGISTRATION_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<?php echo $this->html( 'grid.boolean' , 'registration' , $profile->registration , 'registration' ); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_COMMUNITY_ACCESS');?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_PROFILES_FORM_COMMUNITY_ACCESS'), JText::_('COM_EASYSOCIAL_PROFILES_FORM_COMMUNITY_ACCESS_DESC'), 'bottom'); ?>
						></i>
					</label>
					<div class="col-md-8">
						<?php echo $this->html('grid.boolean', 'community_access', $profile->community_access, 'community_access'); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_ORDERING' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_ORDERING' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_ORDERING_DESCRIPTION' ) , 'bottom' ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<input type="text" class="form-control input-sm input-short text-center" name="ordering" value="<?php echo $profile->ordering; ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_LAYOUT');?></b>
				<p class="panel-info"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_LAYOUT_INFO');?></p>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="theme" class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DEFAULT_THEME' );?>
						<i class="fa fa-question-circle pull-right"
							<?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DEFAULT_THEME' ) , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DEFAULT_THEME_DESCRIPTION' ) , 'bottom' , JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_DESCRIPTION_PLACEHOLDER' ) ); ?>
						></i>
					</label>
					<div class="col-md-8">
						<select name="params[theme]" id="theme" class="form-control input-sm">
							<option value=""<?php echo $param->get( 'theme' ) == '' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_USE_DEFAULT' ); ?></option>
							<?php foreach( $themes as $theme ){ ?>
							<option value="<?php echo $theme->element;?>"<?php echo strtolower( $theme->element ) == strtolower( $param->get( 'theme' ) ) ? ' selected="selected"' : '';?>><?php echo JText::_( $theme->name ); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_GROUPS' );?></b>
				<p class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_GROUPS_DESC' );?></p>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="theme" class="col-md-4">
						<?php echo JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_GROUPS_DEFAULT_USER_GROUP' );?>
					</label>
					<div class="col-md-8">
						<?php echo $this->html( 'tree.groups' , 'gid' , $profile->gid , $guestGroup ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
