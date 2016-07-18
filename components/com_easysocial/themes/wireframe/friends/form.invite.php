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
	<h3><?php echo JText::_('COM_EASYSOCIAL_HEADING_INVITE_FRIENDS'); ?></h3>
	<p><?php echo JText::_('COM_EASYSOCIAL_HEADING_INVITE_FRIENDS_DESC'); ?></p>
</div>

<form id="listForm" method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal">
	<div class="es-container">
		<div class="controls-group-wrap">

			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITE_EMAIL_ADDRESSES'); ?>:</label>

				<div class="controls">
					<textarea class="form-control input-sm" name="emails" name="emails" placeholder="john@email.com"></textarea>
				</div>

				<div class="controls">
					<div class="help-block small mt-5">
						<strong><?php echo JText::_('COM_EASYSOCIAL_NOTE');?>:</strong> <?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITE_EMAIL_ADDRESSES_NOTE');?>
					</div>
				</div>
			</div>


			<div class="control-group">
				<label class="control-label"><?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITE_MESSAGE'); ?>: </label>

				<div class="controls">
					<?php echo $editor->display('message', JText::sprintf('COM_EASYSOCIAL_FRIENDS_INVITE_MESSAGE_CONTENT', FD::jconfig()->sitename), '100%', '200', '10', '5', array('image', 'pagebreak', 'ninjazemanta', 'article', 'readmore'), null, 'com_easysocial'); ?>
				</div>

				<div class="controls">
					<div class="help-block small mt-5">
						<strong><?php echo JText::_('COM_EASYSOCIAL_NOTE');?>:</strong> <?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITE_MESSAGE_NOTE');?>
					</div>
				</div>
			</div>

		</div>

		<!-- Actions -->
		<div class="form-actions">
			<button class="btn btn-sm btn-es-primary pull-right"><?php echo JText::_('COM_EASYSOCIAL_SUBMIT_BUTTON');?></button>
			<a href="<?php echo FRoute::friends();?>" class="btn btn-es btn-sm pull-right mr-5"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></a>
		</div>

		<input type="hidden" name="controller" value="friends" />
		<input type="hidden" name="task" value="sendInvites" />
		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="<?php echo FD::token();?>" value="1" />
	</div>
</form>
