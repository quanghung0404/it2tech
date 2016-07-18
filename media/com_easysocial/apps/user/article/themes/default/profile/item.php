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
<li class="article-item" data-article-list-item>
	<h4>
		<a href="<?php echo $article->permalink;?>"><?php echo $article->title; ?></a>
	</h4>

	<div class="article-meta">
		<i class="fa fa-calendar"></i> <?php echo $this->html( 'string.date' , $article->created , JText::_( 'DATE_FORMAT_LC3' ) ); ?>
		<?php echo JText::sprintf( 'APP_USER_ARTICLE_IN' , '<a href="' . $article->category->permalink . '">' . $article->category->title . '</a>' ); ?>
	</div>

	<div class="mb-10 mt-10 blog-description">
		<?php if( isset($article->image) ){ ?>
			<a href="<?php echo $article->permalink;?>" class="blog-image pull-left mr-10">
				<img src="<?php echo $article->image; ?>" align="left" width="96" />
			</a>
		<?php } ?>
		<?php echo $article->content; ?>
	</div>

	<div class="article-actions">
		<a href="<?php echo $article->permalink;?>" class="btn btn-es btn-sm"><?php echo JText::_( 'APP_ARTICLE_CONTINUE_READING' ); ?> &rarr;</a>
	</div>
</li>
