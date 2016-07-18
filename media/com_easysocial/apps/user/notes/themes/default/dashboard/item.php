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
<li class="note-item" data-apps-notes-item data-id="<?php echo $note->id;?>">

	<div class="clearfix">
		<h4 class="pull-left">
			<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'id' => $app->alias , 'cid' => $note->id , 'uid' => $user->getAlias() , 'type' => SOCIAL_TYPE_USER ) );?>" class="note-title"><?php echo $note->title; ?></a>
		</h4>
		<div class="pull-right btn-group">
			<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ loginLink btn btn-dropdown">
				<i class="icon-es-dropdown"></i>
			</a>

			<ul class="dropdown-menu dropdown-menu-user messageDropDown">
				<li>
					<a href="javascript:void(0);" data-apps-notes-edit>
						<?php echo JText::_( 'APP_NOTES_EDIT_BUTTON' );?>
					</a>
				</li>
				<li data-friends-unfriend="">
					<a href="javascript:void(0);" data-apps-notes-delete data-id="<?php echo $note->id;?>">
						<?php echo JText::_( 'APP_NOTES_DELETE_BUTTON' );?>
					</a>
				</li>
			</ul>
		</div>
	</div>

	<hr />

	<div class="muted small note-date">
		<time datetime="<?php echo $this->html( 'string.date' , $note->created ); ?>" class="note-date">
			<i class="fa fa-calendar"></i>&nbsp; <?php echo $this->html( 'string.date' , $note->created , JText::_( 'DATE_FORMAT_LC1' ) ); ?>
		</time>
	</div>
</li>
