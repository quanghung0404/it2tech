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
<li class="stream actions clearfix conversationActions" style="">
	<div class="btn-group pull-right mr-10">
		<a rel="tooltip" href="javascript:void(0);" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_ALL_BUTTON' );?>" class="btn filterItem active">
			<i class="icon-flag"></i>
		</a>
		<a rel="tooltip" href="javascript:void(0);" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_UNREAD_BUTTON' );?>" data-filter="unread" class="btn filterItem">
			<i class="icon-flag"></i>
		</a>
		<a rel="tooltip" href="javascript:void(0);" data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_READ_BUTTON' );?>" data-filter="read" class="btn filterItem">
			<i class="icon-flag"></i>
		</a>
	</div>

	<label class="checkbox mr-10 mt-5">
		<input type="checkbox" class="item-check checkAll" name="checkAll" /> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_CHECKALL' ); ?>
	</label>

	<select class="input select width-200 select-action conversationAction" name="conversationAction">
		<option value="archive"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ARCHIVE_SELECTED' );?></option>
		<option value="delete"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_DELETE_SELECTED' );?></option>
		<option value="read"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_READ_SELECTED' );?></option>
		<option value="unread"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_UNREAD_SELECTED' );?></option>
	</select>
</li>
