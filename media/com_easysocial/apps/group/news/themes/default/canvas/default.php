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
<div class="app-news app-groups" data-group-news data-id="<?php echo $group->id;?>">

	<div class="es-filterbar row-table">
		<div class="col-cell filterbar-title"><?php echo JText::_( 'APP_GROUP_NEWS_SUBTITLE' ); ?></div>

		<?php if( $group->isAdmin() || $this->my->isSiteAdmin() ){ ?>
		<div class="col-cell cell-tight">
			<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'form' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() ) );?>" class="btn btn-es-primary btn-sm pull-right">
				<?php echo JText::_( 'APP_GROUP_NEWS_NEW' ); ?>
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="app-contents-wrap">
		<div class="group-news-contents app-contents<?php echo !$items ? ' is-empty' : '';?>" data-group-news-contents>
			<ul class="fd-reset-list group-news-items">
				<?php foreach( $items as $article ){ ?>
				<li>
					<div class="media">
						<div class="media-object pull-left">
							<img src="<?php echo FD::user( $article->created_by )->getAvatar(); ?>" class="es-avatar" />
						</div>

						<div class="media-body">
							<h3>
								<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'newsId' => $article->id ) , false );?>"><?php echo $article->get( 'title' ); ?></a>
							</h3>

							<div class="group-news-meta">
								<ul class="fd-reset-list">
									<?php if( $params->get( 'display_author' , true ) ){ ?>
									<li>
										<i class="fa fa-user"></i> <a href="<?php echo FD::user( $article->created_by )->getPermalink(); ?>"><?php echo FD::user( $article->created_by )->getName();?></a>
									</li>
									<?php } ?>

									<?php if( $params->get( 'display_date' , true ) ){ ?>
									<li>
										<i class="fa fa-calendar"></i> <?php echo FD::date( $article->created )->format( JText::_( 'DATE_FORMAT_LC' ) );?>
									</li>
									<?php }?>

									<?php if( $params->get( 'display_hits' , true ) ){ ?>
									<li>
										<i class="fa fa-eye"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'APP_GROUP_NEWS_HITS' , $article->hits ) , $article->hits ); ?>
									</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>

					<p class="news-snippet">
						<?php echo $article->content; ?>
					</p>

					<div class="group-news-meta">
						<ul class="fd-reset-list">
							<?php if( $params->get( 'allow_comments' ) && $article->comments ){ ?>
							<li>
								<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'newsId' => $article->id ) , false );?>#comments"><?php echo JText::_( 'APP_GROUP_NEWS_COMMENT' ); ?></a>
							</li>
							<?php } ?>
							<li>
								<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'item' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() , 'newsId' => $article->id ) , false );?>">
									<?php echo JText::_( 'APP_GROUP_NEWS_READ_ON' ); ?> &rarr;
								</a>
							</li>
						</ul>
					</div>

				</li>
				<?php } ?>
			</ul>

			<div class="empty empty-hero">
				<i class="fa fa-droplet"></i>
				<?php echo JText::_( 'APP_GROUP_NEWS_EMPTY' ); ?>
			</div>

			<div class="es-pagination-footer">
				<?php echo $pagination->getListFooter( 'site' ); ?>
			</div>
		</div>

	</div>

</div>
