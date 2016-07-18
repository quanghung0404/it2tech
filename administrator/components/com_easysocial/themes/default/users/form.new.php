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
<form name="adminForm" id="adminForm" class="profileForm" method="post" enctype="multipart/form-data" data-user-form>

<div class="app-filter filter-bar form-inline">
	<div class="form-group">
		<label for="profileType">
			<i data-placement="bottom" data-title="<?php echo JText::_( 'COM_EASYSOCIAL_USER_SELECT_PROFILE_TYPE' );?>" data-content="<?php echo JText::_( 'COM_EASYSOCIAL_USER_SELECT_PROFILE_TYPE_DESC' );?>" data-es-provide="popover" class="fa fa-question-circle pull-right" data-original-title=""></i>
			<?php echo JText::_('COM_EASYSOCIAL_USER_SELECT_PROFILE_TYPE_FOR_NEW_USER'); ?>
		</label>
	</div>
	<div class="form-group">
		<div class="input-group">
			<input type="text" class="input-sm form-control required" size="40" disabled="disabled" readonly="readonly" aria-required="true" required="required" value="<?php echo $profile->get('title');?>" data-profile-title />
			<span class="input-group-btn">
				<a class="btn btn-es-primary btn-sm" data-user-select-profile>
					<?php echo JText::_('COM_EASYSOCIAL_SELECT_A_PROFILE'); ?>
				</a>
			</span>
		</div>
	</div>

	<div class="form-group">
		<div class="checkbox">
			<label for="autoapproval">
				<input type="checkbox" id="autoapproval" name="autoapproval" value="1" /> <?php echo JText::_('COM_EASYSOCIAL_USER_AUTOMATICALLY_APPROVE_USER');?>
			</label>
		</div>
	</div>
</div>

<div data-user-new-content>
	<?php echo $this->includeTemplate('admin/users/form.new.content'); ?>
</div>

<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="users" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="profileId" value="<?php echo $profile->id;?>" />
<?php echo JHTML::_( 'form.token' );?>

</form>
