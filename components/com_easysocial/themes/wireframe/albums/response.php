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
<div class="es-album-response">
	<div class="es-action-wrap">
		<ul class="fd-reset-list es-action-feedback">
			<li>
				<span class="fd-small item-lapsed">
					<time class="fd-small">
						<?php echo FD::date( $album->created )->toLapsed(); ?>
					</time>
				</span>
			</li>
			<li>
				<?php echo $likes->button();?>
			</li>
			<li>
				<a href="javascript:void(0);" class="fd-small"><?php echo $shares->button();?></a>
			</li>
			<?php if ($lib->hasPrivacy()) { ?>
			<li class="es-action-privacy">
				<?php
					$isHtml = ( $album->id ) ? false : true;
					echo $privacy->form( $album->id, SOCIAL_TYPE_ALBUM, $album->uid, 'albums.view', $isHtml );
				?>
			</li>
			<?php } ?>
		</ul>
	</div>
	<div data-stream-counter class="es-stream-counter<?php echo ( $shares->getCount() == 0 && $likes->getCount() == 0 ) ? ' hide' : ''; ?>">
		<div class="es-stream-actions"><?php echo $likes->toHTML(); ?></div>
		<div class="es-stream-actions pull-right"><?php echo $shares->toHTML(); ?></div>
	</div>
	<div class="es-stream-actions">
		<?php echo $comments->getHTML( array( 'hideEmpty' => false ) );?>
	</div>
</div>
