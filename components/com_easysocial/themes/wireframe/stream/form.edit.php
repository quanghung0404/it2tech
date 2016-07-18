<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="stream-filter" data-stream-filter-form>
	<form method="post" action="<?php echo JRoute::_( 'index.php' );?>" class="form-horizontal" data-filter-inputForm>

	<div class="stream-filter-heading">
		<h3>
			<?php echo (! $filter->id ) ? JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_CREATE_NEW_FILTER' ) : JText::sprintf( 'COM_EASYSOCIAL_STREAM_FILTER_EDIT_FILTER',  $filter->title ); ?>
		</h3>
		<hr />
	</div>

	<div class="stream-filter-contents">
		<p class="small mb-20">
			<?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_DESCRIPTION' ); ?>
		</p>

		<div class="alert" filter-form-notice style="display:none;"></div>

		<div class="mb-20">
			<input type="text" name="title" value="<?php echo $filter->title; ?>" data-filter-name class="form-control input-sm" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_TITLE_PLACEHOLDER' , true ); ?>" />

			<div class="">
				<div class="help-block small text-note">
					<span class="label label-danger"><?php echo JText::_( 'COM_EASYSOCIAL_NOTE' );?>:</span> <?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_TITLE_DESC' ); ?>
				</div>
			</div>
		</div>

		<div class="">
			<input type="text" name="hashtag" value="<?php echo $filter->getHashTag( true ); ?>" data-filter-hashtag class="form-control input-sm" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_HASHTAG_PLACEHOLDER' , true ); ?>" />
			<div>
				<div class="help-block small text-note">
					<span class="label label-danger"><?php echo JText::_( 'COM_EASYSOCIAL_NOTE' );?>:</span> <?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_HASHTAG_DESC' ); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<div class="pull-right">
			<button type="button" class="btn btn-sm btn-es-primary" data-stream-filter-save><?php echo JText::_( 'COM_EASYSOCIAL_SAVE_FILTER_BUTTON' );?></button>

		</div>

		<?php if( $filter->id ) { ?>
		<div class="pull-left">
			<button
				class="btn btn-sm btn-es-danger"
				data-stream-filter-delete
				onclick="return false;"
				data-id="<?php echo $filter->id; ?>"
				<?php if( isset( $uid ) && $uid ){ ?>
				data-uid="<?php echo $uid; ?>"
				data-utype="<?php echo $filter->utype; ?>"
				<?php } ?>
			>
				<?php echo JText::_( 'COM_EASYSOCIAL_DELETE_FILTER_BUTTON' );?>
			</button>
		</div>
		<?php } ?>
	</div>

	<?php if( isset( $uid ) && $uid ){ ?>
	<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
	<?php } ?>

	<?php if( isset( $controller ) && $controller ){ ?>
	<input type="hidden" name="controller" value="<?php echo $controller; ?>" />
	<?php } else { ?>
	<input type="hidden" name="controller" value="stream" />
	<?php } ?>

	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="task" value="saveFilter" />
	<input type="hidden" name="id" value="<?php echo $filter->id; ?>" />
	<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
