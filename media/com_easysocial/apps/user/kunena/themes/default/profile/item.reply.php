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
<li class="post-item">
	<div class="row">
		<div class="col-md-12">
			<div class="pull-left">
				<span class="vote-count"
					data-original-title="<?php echo JText::_( 'APP_KUNENA_TOTAL_HITS' );?>"
					data-es-provide="tooltip"
					data-placement="bottom"
				><?php echo $post->hits;?></span>
			</div>

			<div class="post-info">
				<div class="post-title">
					<a href="#"><?php echo $post->subject ?></a>
				</div>

				<div class="post-meta">
					<?php echo JText::_( 'APP_USER_KUNENA_STREAM_CONTENT_POSTED_IN' , '<a href="#">' . $post->name . '</a>' ); ?>
				</div>
			</div>
		</div>
	</div>
</li>
