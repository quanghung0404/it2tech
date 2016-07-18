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
<h2 class="page-title reset-h"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ARCHIVES_TITLE' );?></h2>

<div class="conversation">

	<ul id="conversationList" class="streams for-respond reset-list">

		<?php echo $this->loadTemplate( 'site/conversations/actions' );?>

		<?php if( $conversations ){ ?>
			<?php foreach( $conversations as $conversation ){ ?>
				<?php echo $this->loadTemplate( 'site/conversations/item' , array( 'conversation' => $conversation , 'participants' => $conversation->getParticipants( $this->my->id ) ) ); ?>
			<?php } ?>
		<?php } else { ?>
			<li class="stream empty">
				<?php echo JText::_( 'COM_EASYSOCIAL_NO_ARCHIVED_CONVERSATIONS_YET' );?>
			</li>
		<?php } ?>

	</ul>

	<hr />

	<div class="navigations clearfix">
		<div class="links btn-group pull-right">
			<a href="#" class="btn disabled"><i class="icon-chevron-left"></i></a>
			<a href="#" class="btn"><i class="icon-chevron-right"></i></a>
		</div>

		<span class="current small mute">
			Page 1 of 4
		</span>
	</div>

</div>
