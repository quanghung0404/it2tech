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
<div data-field-relationship class="data-field-relationship">

	<?php if( $relation ) { ?>
	<div data-relationship-display data-relationship-display-confirm class="data-relationship-display" data-id="<?php echo $relation->id; ?>">
		<div data-relationship-display-info class="data-relationship-display-info">
			<div data-relationship-display-type class="data-relationship-display-type">
				<span data-relationship-display-type-label><?php echo $relation->getLabel(); ?></span>
				<?php if( $relation->isConnect() && !empty( $relation->target ) ) { ?>
					<span data-relationship-display-type-connectword><?php echo $relation->getConnectWord(); ?></span>
				<?php } else { ?>
					<a class="btn-delete" href="javascript:void(0);" data-relationship-display-actions-delete>×</a>
				<?php } ?>
			</div>
			<?php if( $relation->isConnect() && !empty( $relation->target ) ) { ?>
			<div class="media">
				<div class="media-object pull-left">
					<div data-relationship-display-target class="data-relationship-display-target">
						<img src="<?php echo $relation->getTargetUser()->getAvatar(SOCIAL_AVATAR_MEDIUM); ?>" data-relationship-display-target-avatar />

					</div>
				</div>
				<div class="media-body">
					<div data-relationship-display-target-name><?php echo $relation->getTargetUser()->getName(); ?></div>
					<?php if( $relation->isPending() ) { ?>
						<div class="label label-warning" data-relationship-display-pending-text><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_PENDING_APPROVAL' ); ?></div>
					<?php } ?>
					<div data-relationship-display-actions class="data-relationship-display-actions">
						<a class="btn-delete" href="javascript:void(0);" data-relationship-display-actions-delete <?php echo $relation && ( $relation->isActor( $user->id ) || ( $relation->isTarget( $user->id ) && $relation->isApproved() ) ) ? '' : 'style="display: none;"'; ?> >×</a>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>

		<div data-relationship-display-loading style="display: none;">
			<span data-relationship-display-loading-text><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_ACTION_WORKING' ); ?></span>
		</div>

		<div data-relationship-display-error style="display: none;">
			<span class="label label-danger" data-relationship-display-error-text><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_ERROR_OCCURED' ); ?></span>
		</div>
	</div>
	<?php } ?>

	<div data-relationship-form <?php echo $relation ? 'style="display: none;"' : ''; ?> data-orig-type="<?php echo $relation ? $relation->type : ''; ?>" data-orig-target="<?php echo $relation ? $relation->target : 0; ?>" data-orig-approved="<?php echo $relation ? $relation->state : 0; ?>">
		<select class="form-control input-sm" data-relationship-form-type name="<?php echo $inputName; ?>[type]">
		<?php foreach( $types as $type ) { ?>
			<option value="<?php echo $type->value; ?>" <?php if( $relation && $relation->type === $type->value ) { ?>selected="selected"<?php } ?>><?php echo JText::_( $type->label ); ?></option>
		<?php } ?>
		</select>
		<span data-relationship-form-connectwords>
		<?php $i = 0; ?>
		<?php foreach( $types as $type ) { ?>
			<span data-relationship-form-connectword="<?php echo $type->value; ?>" data-relationship-form-connectword-<?php echo $type->value; ?> <?php if( ( $relation && $relation->type !== $type->value ) || ( !$relation && !$firstType->connect ) ) { ?>style="display: none;"<?php } ?>><?php echo $type->connectword; ?></span>
			<?php $i++; ?>
		<?php } ?>
		</span>

		<div class="mt-5" data-relationship-form-input <?php if( !empty( $relation->target ) || ( $relation && !$relation->isConnect() ) || ( !$relation && !$firstType->connect ) ) { ?>style="display: none;"<?php } ?>>
			<div class="textboxlist">
				<?php if( $relation && $relation->isConnect() && !empty( $relation->target ) ) { ?>
				<div class="textboxlist-item" data-textboxlist-item="" data-id="<?php echo $relation->getTargetUser()->id; ?>"><span class="textboxlist-itemContent" data-textboxlist-itemcontent=""><?php echo $relation->getTargetUser()->getName(); ?><input type="hidden" name="<?php echo $inputName; ?>[target][]" value="<?php echo $relation->getTargetUser()->id; ?>"></span><div class="textboxlist-itemRemoveButton" data-textboxlist-itemremovebutton="">×</div></div>
				<?php } ?>
				<input data-relationship-form-input-field type="text" class="textboxlist-textField" data-textboxlist-textField />
			</div>
		</div>

		<div class="data-relationship-form-target" data-relationship-form-target <?php if( empty( $relation->target ) ) { ?>style="display: none;"<?php } ?>>
			<div class="media">
				<div class="media-object pull-left">
					<img data-relationship-form-target-avatar src="<?php echo !empty( $relation->target ) ? $relation->getTargetUser()->getAvatar(SOCIAL_AVATAR_MEDIUM) : ''; ?>" />
				</div>
				<div class="media-body">
					<div data-relationship-form-target-name><?php echo !empty( $relation->target ) ? $relation->getTargetUser()->getName() : ''; ?></div>
					<div class="label label-warning" data-relationship-form-target-pending <?php if( $relation && $relation->state ) { ?>style="display: none;"<?php } ?>><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_REQUIRES_APPROVAL' ); ?></div>
					<a class="btn-delete" href="javascript: void(0);" data-relationship-form-target-delete <?php if( !empty( $relation->target ) ) { ?>data-id="<?php echo $relation->getTargetUser()->id; ?>"<?php } ?>>×</a>
				</div>
			</div>


		</div>
	</div>

	<?php if( !empty( $pending ) ) { ?>
	<div data-relationship-pending-title>
		<h5><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_RELATIONSHIP_REQUEST' ); ?></h5>
	</div>
	<?php } ?>

	<?php foreach( $pending as $p ) { ?>
	<div data-relationship-display data-relationship-display-pending class="data-relationship-display" data-id="<?php echo $p->id; ?>">
		<div data-relationship-display-info class="data-relationship-display-info">
			<div data-relationship-display-type class="data-relationship-display-type">
				<span data-relationship-display-type-label><?php echo $p->getLabel(); ?></span>
				<span data-relationship-display-type-connectword><?php echo $p->getConnectWord(); ?></span>
			</div>
			<div class="media">
				<div class="media-object pull-left">
					<div data-relationship-display-target class="data-relationship-display-target">
						<img src="<?php echo $p->getActorUser()->getAvatar(SOCIAL_AVATAR_MEDIUM); ?>" data-relationship-display-target-avatar />

					</div>
				</div>
				<div class="media-body">
					<div data-relationship-display-target-name><?php echo $p->getActorUser()->getName(); ?></div>
					<?php if( $p->isPending() ) { ?>
						<div class="label label-warning" data-relationship-display-pending-text><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_PENDING_APPROVAL' ); ?></div>
					<?php } ?>
					<div data-relationship-display-actions class="data-relationship-display-actions">
						<button class="btn-reject btn btn-es btn-small" type="button" data-relationship-display-actions-reject><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_ACTION_REJECT' ); ?></button>
						<button class="btn-approve btn btn-es-primary btn-small" type="button" data-relationship-display-actions-approve><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_ACTION_APPROVE' ); ?></button>

						<a class="btn-delete" href="javascript:void(0);" data-relationship-display-actions-delete <?php if( $p->isPending() ) { ?>style="display: none;"<?php } ?>>×</a>
					</div>
				</div>
			</div>
		</div>

		<div data-relationship-display-loading style="display: none;">
			<span data-relationship-display-loading-text><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_ACTION_WORKING' ); ?></span>
		</div>

		<div data-relationship-display-error style="display: none;">
			<span class="label label-danger" data-relationship-display-error-text><?php echo JText::_( 'PLG_FIELDS_RELATIONSHIP_ERROR_OCCURED' ); ?></span>
		</div>
	</div>
	<?php } ?>
</div>
