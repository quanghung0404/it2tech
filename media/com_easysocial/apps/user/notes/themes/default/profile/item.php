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
	<h4 class="es-stream-content-title">
		<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=apps&layout=canvas&id=' . $appId . '&cid=' . $note->id . '&userid=' . $user->id );?>" class="note-title"><?php echo $note->title; ?></a>
	</h4>

	<div class="note-meta">
		<time datetime="<?php echo $this->html( 'string.date' , $note->created ); ?>" class="note-date">
			<span>
				<i class="fa fa-calendar "></i> <?php echo $this->html( 'string.date' , $note->created , JText::_( 'DATE_FORMAT_LC1' ) ); ?>
			</span>
		</time>
	</div>

	<div class="note-excerpt">
		<?php echo $note->getContent();?>
	</div>

	<div class="es-action-wrap">
		<ul class="list-unstyled es-action-feedback">
			<li>
				<a href="javascript:void(0);"><?php echo $note->likes->button();?></a>
			</li>
		</ul>

		<div data-stream-counter class="es-stream-counter<?php echo ( $note->likes->getCount() == 0 ) ? ' hide' : ''; ?>">
			<div class="es-stream-actions"><?php echo $note->likes->toHTML(); ?></div>
		</div>
		<div class="es-stream-actions">
			<?php echo $note->comments->getHTML( array( 'hideEmpty' => false ) );?>
		</div>
	</div>

</li>
