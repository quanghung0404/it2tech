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
<form name="conversation-compose" class="form-horizontal" enctype="multipart/form-data" method="post" data-conversations-composer data-composer-form>
<div class="es-container">

	<div class="controls-group-wrap mt-20">

		<!-- Participants -->
		<div class="control-group" data-composer-recipients>
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_RECIPIENTS' ); ?>:</label>

			<div class="textboxlist controls disabled" data-friends-suggest>
				<input type="text" autocomplete="off" disabled class="participants textboxlist-textField" data-textboxlist-textField placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_START_TYPING' );?>" data-textboxlist-textField />
			</div>
		</div>

		<!-- Editor kicks in here -->
		<div class="control-group" data-composer-message>
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_MESSAGE' ); ?>:</label>

			<div class="controls">
				<div class="composer-textarea input-wrap" data-composer-editor-header>
					<div class="es-story-textbox mentions-textfield" data-composer-editor-area>
						<div class="mentions">
							<div data-mentions-overlay data-default="<?php echo $this->html( 'string.escape' , $message ); ?>"><?php echo $message; ?></div>
							<textarea class="input-sm input-shape form-control" name="message" autocomplete="off"
								data-mentions-textarea
								data-default="<?php echo $this->html( 'string.escape' , $message );?>"
								data-initial="0"
								data-composer-editor
								placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_MESSAGE_PLACEHOLDER' );?>"><?php echo $message; ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if( $this->config->get( 'conversations.attachments.enabled' ) || $this->config->get( 'conversations.location' ) ){ ?>
		<div class="controls">
			<div class="composer-attach">

				<?php if( $this->config->get( 'conversations.attachments.enabled' ) ){ ?>
				<!-- File attachments -->
				<div class="attachment-service" data-composer-attachment>
					<?php echo $this->loadTemplate( 'site/uploader/form' , array( 'size' => $this->config->get( 'conversations.attachments.maxsize' ) ) ); ?>
				</div>
				<?php } ?>

			</div>
		</div>
		<?php } ?>

		<div class="form-actions">
			<div class="pull-right">
				<a href="<?php echo FRoute::conversations();?>" class="btn btn-es btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></a>
				<button class="btn btn-es-primary btn-sm" data-composer-submit><?php echo JText::_( 'COM_EASYSOCIAL_SUBMIT_BUTTON' );?></button>
			</div>
		</div>

		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="controller" value="conversations" />
		<input type="hidden" name="task" value="store" />
		<input type="hidden" name="<?php echo FD::token();?>" value="1" />
	</div>
</div>
</form>
