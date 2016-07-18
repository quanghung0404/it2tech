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
<div class="app-article" data-article>

	<div class="app-contents<?php echo !$articles ? ' is-empty' : '';?>">
		<ul class="list-unstyled article-list" data-article-lists>
			<?php if( $articles ){ ?>
				<?php foreach( $articles as $article ){ ?>
					<?php echo $this->loadTemplate( 'themes:/apps/user/article/profile/item' , array( 'article' => $article ) ); ?>
				<?php } ?>
			<?php } ?>
		</ul>

		<div class="empty">
			<i class="fa fa-droplet"></i>
			<?php echo JText::sprintf( 'APP_ARTICLE_PROFILE_NO_ARTICLES_CURRENTLY', $user->getName() ); ?>
		</div>

	</div>

</div>
