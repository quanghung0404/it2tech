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
	<h5>
		<a href="<?php echo $article->permalink;?>"><?php echo $article->title; ?></a>
	</h5>

	<div class="article-item-meta">
		<div class="col-md-12">
			<div class="pull-left">
				<span class="in">
					<?php echo JText::_( 'APP_ARTICLE_IN' ); ?>
				</span>
				<span class="pull-lefblog-item-meta-category">
					<a href="<?php echo $article->category->permalink;?>"><?php echo $article->category->title;?></a>
				</span>
			</div>

			<div class="pull-right">
				<i class="icon-es-calendar"></i>
				<span class="fd-small"><?php echo $this->html( 'string.date' , $article->created , 'd/m/Y'); ?></span>
			</div>
		</div>

	</div>

	<hr />
	<div class="article-text">
		<?php echo $article->content; ?>
	</div>

	<div class="article-item-actions mt-15">

		<div class="article-item-actions-readmore pull-right fd-small">
			<a href="<?php echo $article->permalink;?>"><?php echo JText::_( 'APP_ARTICLE_CONTINUE_READING' ); ?></a>
		</div>
	</div>

</li>
