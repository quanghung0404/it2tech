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
<div class="view-heading mb-20">
<?php if( $list->id ){ ?>
	<h3><?php echo JText::_( 'COM_EASYSOCIAL_HEADING_EDIT_FRIEND_LIST' ); ?></h3>
	<p><?php echo JText::_( 'COM_EASYSOCIAL_HEADING_EDIT_FRIEND_LIST_DESC' ); ?></p>
<?php } else { ?>
	<h3><?php echo JText::_( 'COM_EASYSOCIAL_HEADING_NEW_FRIEND_LIST' ); ?></h3>
	<p><?php echo JText::_( 'COM_EASYSOCIAL_HEADING_NEW_FRIEND_LIST_DESC' ); ?></p>
<?php } ?>
</div>

<form id="listForm" method="post" action="<?php echo JRoute::_( 'index.php' );?>" class="form-horizontal">
<div class="es-container">
	<div class="controls-group-wrap">

		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_TITLE' ); ?>:</label>

			<div class="controls">
				<input type="text" class="form-control input-sm" name="title" value="<?php echo $this->html( 'string.escape' , $list->title );?>"
					placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_TITLE_PLACEHOLDER' );?>" />
			</div>

			<div class="controls">
				<div class="help-block small mt-5">
					<strong><?php echo JText::_( 'COM_EASYSOCIAL_NOTE' );?>:</strong> <?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_TITLE_NOTE' );?>
				</div>
			</div>
		</div>


		<div class="control-group">
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_USERS' ); ?>: </label>

			<div class="textboxlist controls disabled" data-friends-suggest>
				<?php if( $members ){ ?>
					<?php foreach( $members as $user ){ ?>
						<div class="textboxlist-item" data-id="<?php echo $user->id;?>" data-title="<?php echo $this->html( 'string.escape' , $user->getName() );?>" data-textboxlist-item>
							<span class="textboxlist-itemContent" data-textboxlist-itemContent><?php echo $this->html( 'string.escape' , $user->getName() );?>
								<input type="hidden" name="items[]" value="<?php echo $user->id;?>" />
							</span>
							<a class="textboxlist-itemRemoveButton" href="javascript: void(0);" data-textboxlist-itemRemoveButton><i class="fa fa-remove"></i></a>
						</div>
					<?php } ?>
				<?php } ?>
				<input type="text" autocomplete="off" disabled class="participants textboxlist-textField" data-textboxlist-textField
					placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_USERS_PLACEHOLDER' );?>" />
			</div>

			<div class="controls">
				<div class="help-block small mt-5">
					<strong><?php echo JText::_( 'COM_EASYSOCIAL_NOTE' );?>:</strong> <?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_USERS_NOTE' );?>
				</div>
			</div>

		</div>

		<div class="controls">
            <div class="checkbox">
                <label for="defaultList">
                    <input type="checkbox" name="default" id="defaultList" value="1" />
                    <span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_LIST_FORM_SET_AS_DEFAULT_LIST' ); ?></span>
                </label>
            </div>

		</div>

	</div>

	<!-- Actions -->
	<div class="form-actions">
		<button class="btn btn-es-primary pull-right"><?php echo JText::_( 'COM_EASYSOCIAL_SUBMIT_BUTTON' );?></button>
		<a href="<?php echo FRoute::friends();?>" class="btn btn-es pull-right mr-5"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></a>
	</div>

	<input type="hidden" name="controller" value="friends" />
	<input type="hidden" name="task" value="storeList" />
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="<?php echo FD::token();?>" value="1" />
</div>
</form>
