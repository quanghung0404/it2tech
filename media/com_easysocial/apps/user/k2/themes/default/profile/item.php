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
<li class="k2-item" data-article-list-item>
	<h4>
		<a href="<?php echo $item->permalink;?>"><?php echo $item->title; ?></a>
	</h4>

	<div class="k2-meta">
		<?php echo JText::sprintf( 'APP_USER_K2_IN' , '<a href="' . $item->category->permalink . '">' . $item->category->name . '</a>' ); ?>
		&nbsp;&middot;&nbsp;
		<i class="fa fa-calendar"></i> <?php echo $this->html( 'string.date' , $item->created , JText::_( 'DATE_FORMAT_LC3' ) ); ?>
	</div>

	<div class="k2-text">
		<div class="media">
			<?php if ($item->image) { ?>
			<div class="media-object pull-right">
				<img src="<?php echo $item->image;?>" />
			</div>
			<?php } ?>
			
			<div class="media-body">
				<?php echo $item->content; ?>
			</div>
		</div>
	</div>

	<div class="k2-actions">
		<a href="<?php echo $item->permalink;?>" class="btn btn-es btn-sm"><?php echo JText::_( 'APP_ARTICLE_CONTINUE_READING' ); ?> &rarr;</a>
	</div>
</li>
