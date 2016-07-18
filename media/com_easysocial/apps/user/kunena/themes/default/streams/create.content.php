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
<div class="stream-kunena mt-10 mb-10">
	<div class="media">

		<?php if( $topic->getIcon() ){ ?>
		<div class="media-object pull-left mr-15">
			<?php echo $topic->getIcon();?>
		</div>
		<?php } ?>

		<div class="media-body">
			<h4 class="es-stream-content-title">
				<a href="<?php echo $topic->getUrl();?>"><?php echo $topic->subject;?></a>
			</h4>

			<p><?php echo $topic->message;?></p>

			<div>
				<a href="<?php echo $topic->getPermaUrl();?>"><?php echo JText::_( 'APP_KUNENA_BTN_VIEW_THREAD' ); ?></a>
			</div>
		</div>
	</div>
</div>
