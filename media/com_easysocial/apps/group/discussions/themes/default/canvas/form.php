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
<div class="app-discussions app-group">
	<div class="es-content group-discussion-create">
		<div class="es-content-wrap">
			<form action="<?php echo JRoute::_( 'index.php' );?>" method="post">

				<?php if( $discussion->id ){ ?>
					<h3><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_EDITING_SUBTITLE' ); ?></h3>
				<?php } else { ?>
					<h3><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_CREATE_SUBTITLE' ); ?></h3>
				<?php } ?>

				<hr />

				<div>
					<input type="text" name="title" value="<?php echo $this->html( 'string.escape' , $discussion->title );?>"
						placeholder="<?php echo JText::_( 'APP_GROUP_DISCUSSIONS_TITLE_PLACEHOLDER' , true );?>" class="form-control discussion-title mb-10"
					/>

					<div class="editor-wrap fd-cf">
						<?php echo FD::bbcode()->editor( 'content' , $discussion->content , array( 'files' => $files , 'uid' => $group->id , 'type' => SOCIAL_TYPE_GROUP ) ); ?>
					</div>
				</div>

				<div class="form-actions">
					<a href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() ) );?>" class="pull-left btn btn-es-danger btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></a>
					<button type="submit" class="pull-right btn btn-es-primary btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_SUBMIT_BUTTON' ); ?> &rarr;</button>
				</div>

				<?php echo $this->html( 'form.token' ); ?>
				<input type="hidden" name="controller" value="apps" />
				<input type="hidden" name="task" value="controller" />
				<input type="hidden" name="appController" value="discussion" />
				<input type="hidden" name="appTask" value="save" />
				<input type="hidden" name="appId" value="<?php echo $app->id;?>" />
				<input type="hidden" name="cluster_id" value="<?php echo $group->id;?>" />
				<input type="hidden" name="id" value="<?php echo $discussion->id;?>" />
				<input type="hidden" name="option" value="com_easysocial" />
			</form>
		</div>
	</div>
</div>
