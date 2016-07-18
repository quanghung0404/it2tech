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
<div class="mtip rg request-form friendRequestForm">
	<i></i>
	<div>
		<div class="part friend-status">
			<?php echo JText::sprintf( 'COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_PENDING_APPROVAL' , $this->user->getName() );?>
		</div>

		<div class="part friend-message friendRequestWrap">
			<div class="mr-10">
				<textarea id="friend-message" name="created_message" class="textarea width-full requestMessage"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_ADD_MESSAGE' );?></textarea>
				<div class="mt-5" style="text-align: right;">
					<button id="submit-friend-message" class="es-button submitMessage" type="submit"><?php echo JText::_( 'COM_EASYSOCIAL_SUBMIT_BUTTON' );?></button>
				</div>
			</div>
		</div>

		<div class="part friend-list listDropDown" style="background:#f5f5f5">

			<label for="friend-list"><?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_REQUEST_ADD_TO_LIST' );?></label>

			<div class="friend-picker-area mt-10 listItems">
				<ul class="friend-picker-list show reset-list">
					<li class="label selectList">
						<b><?php echo JText::_( 'Select a list' );?></b>
					</li>

					<?php if( $this->lists ){ ?>
						<?php foreach( $this->lists as $list ){ ?>
						<li class="listItem<?php echo $list->default || $list->isContainerFor( $this->user->id , SOCIAL_TYPE_USER ) ? " ticked" : '';?>" data-listid="<?php echo $list->id;?>">
							<i></i>
							<label class="check" for="optionsCheckbox-<?php echo $list->id;?>">
								<input type="checkbox" value="option1" id="optionsCheckbox-<?php echo $list->id;?>" class="optionsCheckbox" />
								<?php echo $list->title;?>
							</label>
						</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
