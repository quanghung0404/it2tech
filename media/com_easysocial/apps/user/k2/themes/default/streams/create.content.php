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
<div class="row mb-10 mt-10">
	<div class="col-md-12">
		<h4>
			<a href="<?php echo $permalink;?>"><?php echo $article->title; ?></a>
		</h4>
		<div class="fd-small">
			<?php echo JText::sprintf( 'APP_USER_K2_STREAM_CONTENT_ARTICLE_META' , '<a href="' . $categoryPermalink . '">' . $category->name .'</a>' , $date->format( JText::_( 'DATE_FORMAT_LC1' ) ) );?>
		</div>

		<p class="mb-10 mt-10 blog-description">
			<?php echo strip_tags( $content ); ?>
		</p>

		<div>
			<a href="<?php echo $permalink;?>" class="mt-5"><?php echo JText::_( 'APP_USER_K2_CONTINUE_READING' ); ?> &rarr;</a>
		</div>
	</div>
</div>
