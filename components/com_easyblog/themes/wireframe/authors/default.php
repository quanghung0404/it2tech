<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($this->params->get('authors_search', true) || $this->params->get('authors_sorting', true)) { ?>
<form name="authors" method="post" action="<?php echo JRoute::_('index.php'); ?>" class="eb-author-filter form-horizontal row-table eb-responsive">
    <?php if ($this->params->get('authors_search', true)) { ?>
    <div class="col-cell">
		<div class="eb-authors-finder input-group">
			<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_BLOGGERS', true);?>" value="<?php echo $this->html('string.escape', $search);?>" />
			<i class="fa fa-user"></i>
			<div class="input-group-btn">
				<button type="button btn btn-primary" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?></button>
			</div>
		</div>
    </div>
    <?php } ?>

    <?php if ($this->params->get('authors_sorting', true)) { ?>
    <div class="col-cell">
        <div class="eb-authors-sorter btn-group pull-right">
          <button type="button" class="btn btn-default dropdown-toggle_" data-bp-toggle="dropdown">
            <?php echo JText::_('COM_EASYBLOG_BLOGGERS_SORT_BY');?> <span class="caret"></span>
          </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=alphabet', false); ?>">
                    <?php if ($sort == 'alphabet') { ?>
                    <i class="fa fa-check"></i>
                    <?php } ?>
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_NAME');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=active', false); ?>">
                        <?php if ($sort == 'active') { ?>
                        <i class="fa fa-check"></i>
                        <?php } ?>
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_ACTIVE');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=latest', false); ?>">
                        <?php if ($sort == 'latest') { ?>
                        <i class="fa fa-check"></i>
                        <?php } ?>
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=latestpost', false); ?>">
                        <?php if ($sort == 'latestpost') { ?>
                        <i class="fa fa-check"></i>
                        <?php } ?>
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST_POST');?>
                    </a>
                </li>
            </ul>
        </div>
	</div>
    <?php } ?>
        <?php echo $this->html('form.action', 'search.blogger'); ?>
</form>
<?php } ?>

<div class="eb-authors" data-authors>
	<?php if ($authors) { ?>
		<?php foreach ($authors as $author) { ?>

            <?php $isFeatured = (isset($author->featured)) ? $author->featured : $author->isFeatured(); ?>

			<div class="eb-author eb-responsive" data-author-item data-id="<?php echo $author->id;?>">
				<div class="eb-authors-head row-table">
                    <?php if ($this->config->get('layout_avatar') && $this->params->get('author_avatar', true)) { ?>
                    <div class="col-cell cell-tight">
                        <a href="<?php echo $author->getPermalink();?>">
                            <img src="<?php echo $author->getAvatar(); ?>" class="eb-avatar eb-authors-avatar" width="50" height="50" alt="<?php echo $author->getName(); ?>" />
                        </a>
                    </div>
                    <?php } ?>

                    <div class="col-cell">
                        <div class="row-table">
                            <div class="col-cell">
                                <h2 class="eb-authors-name reset-heading">
                                    <a href="<?php echo $author->getProfileLink(); ?>" class="text-inherit"><?php echo $author->getName(); ?></a>
                                    <small class="eb-authors-featured eb-star-featured<?php echo !$isFeatured ? ' hide' : '';?>"
                                        data-featured-tag
                                        data-eb-provide="tooltip"
                                        data-original-title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>">
                                        <i class="fa fa-star"></i>
                                    </small>
                                </h2>
                            </div>
        					<?php if (EB::isSiteAdmin()) { ?>
                            <div class="col-cell text-right">
        						<a href="javascript:void(0);" class="btn btn-default<?php echo !$isFeatured ? ' hide' : '';?>" data-author-unfeature data-id="<?php echo $author->id;?>">
        							<i class="fa fa-star-o"></i>&nbsp; <?php echo Jtext::_('COM_EASYBLOG_UNFEATURE_AUTHOR'); ?>
        						</a>
        						<a href="javascript:void(0);" class="btn btn-default<?php echo $isFeatured ? ' hide' : '';?>" data-author-feature data-id="<?php echo $author->id;?>">
        							<i class="fa fa-star"></i>&nbsp; <?php echo Jtext::_('COM_EASYBLOG_FEATURE_AUTHOR'); ?>
        						</a>
                            </div>
        					<?php } ?>
                        </div>

                        <?php if (($author->messaging) || ($author->twitter && $this->params->get('author_twitter', true))) { ?>
                        <div class="eb-authors-meta spans-seperator">
                            <?php if ($author->messaging) { ?>
                            <span><?php echo $author->messaging;?></span>
                            <?php } ?>

                            <?php if ($author->twitter && $this->params->get('author_twitter', true)) { ?>
                            <span>
                                <a class="link-twitter" href="<?php echo $author->twitter; ?>" title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME', true); ?>">
                                    <span class="col-cell figure muted"><i class="fa fa-twitter"></i></span>
                                    <span class="col-cell trait"><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?></span>
                                </a>
                            </span>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
				</div>

				<div class="eb-authors-bio">
					<?php if ($author->getBiography() && $this->params->get('author_bio', true)) { ?>
						<?php echo $author->getBiography();?>
					<?php } ?>

					<div class="eb-authors-subscribe spans-seperator">
						<?php if ($author->getWebsite() && $this->params->get('author_website', true)) { ?>
						<span class="eb-authors-url">
							<a href="<?php echo $this->escape($author->getWebsite());?>" target="_blank"><?php echo $this->escape($author->getWebsite());?></a>
						</span>
						<?php } ?>

						<?php if ($author->id != $this->my->id) {?>
                            <?php if ($this->params->get('author_subscribe_email', true) && $this->config->get('main_bloggersubscription')) { ?>
    							<?php if (!$author->isBloggerSubscribed ) { ?>
    							<span class="eb-authors-subscription">
    								<a href="javascript:void(0);" data-blog-subscribe data-type="blogger" data-id="<?php echo $author->id;?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></a>
    							</span>
    							<?php } else { ?>
    							<span class="eb-authors-subscription">
    								<a href="javascript:void(0);" data-blog-unsubscribe data-type="blogger" data-id="<?php echo $author->id;?>" data-email="<?php echo $this->my->email;?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_BLOGGER'); ?></a>
    							</span>
    							<?php } ?>
                            <?php } ?>

							<?php if ($this->config->get('main_rss') && $this->params->get('author_subscribe_rss', true)) { ?>
							<span class="eb-authors-rss">
								<a href="<?php echo $author->getRSS();?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></a>
							</span>
							<?php } ?>

						<?php }?>
					</div>
				</div>

				<div class="eb-authors-stats">
					<ul class="eb-stats-nav reset-list">
                        <?php if ($this->params->get('author_posts', true)) { ?>
						<li class="active">
							<a class="btn btn-default btn-block" href="#posts-<?php echo $author->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_POSTS');?>
								<b><?php echo $author->blogCount;?></b>
							</a>
						</li>
                        <?php } ?>

                        <?php if ($this->params->get('author_categories', true)) { ?>
						<li>
							<a class="btn btn-default btn-block" href="#categories-<?php echo $author->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_CATEGORIES');?>
								<b><?php echo count($author->categories); ?></b>
							</a>
						</li>
                        <?php } ?>

                        <?php if ($this->params->get('author_tags', true)) { ?>
						<li>
							<a class="btn btn-default btn-block" href="#tags-<?php echo $author->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TAGS');?>
								<b><?php echo count($author->tags);?></b>
							</a>
						</li>
                        <?php } ?>
					</ul>

					<div class="eb-stats-content">
                        <?php if ($this->params->get('author_posts', true)) { ?>
						<div class="tab-pane eb-stats-posts active" id="posts-<?php echo $author->id;?>">
							<?php if ($author->blogs) { ?>
								<?php foreach ($author->blogs as $post) { ?>
								<div>
									<time><?php echo $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC3'));?></time>
									<?php echo $post->getIcon('eb-post-type'); ?>
									<a href="<?php echo EB::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);?>"><?php echo $post->title;?></a>
								</div>
								<?php } ?>

                                <a href="<?php echo $author->getPermalink();?>" class="btn btn-default btn-block btn-show-all">
                                    <?php echo JText::_('COM_EASYBLOG_VIEW_ALL_POSTS');?> &nbsp;<i class="fa fa-chevron-right"></i>
                                </a>
							<?php } else { ?>
                                <div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND');?>
								</div>
							<?php } ?>
						</div>
                        <?php } ?>

                        <?php if ($this->params->get('author_categories', true)) { ?>
						<div class="tab-pane eb-labels eb-stats-categories" id="categories-<?php echo $author->id;?>">
    						<?php if ($author->categories) { ?>
    							<?php foreach ($author->categories as $category) { ?>
    							<a class="btn btn-default" href="<?php echo EB::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id); ?>">
    								<i class="fa fa-folder-open text-muted"></i>
    								&nbsp;
    								<?php echo JText::_($category->title ); ?>
    								(<b><?php echo JText::_($category->post_count); ?></b>)
    							</a>
    							<?php } ?>
    						<?php } else { ?>
    							<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_DID_NOT_CREATE_CATEGORY'); ?></div>
    						<?php } ?>
						</div>
                        <?php } ?>

                        <?php if ($this->params->get('author_tags', true)) { ?>
						<div class="tab-pane eb-labels eb-stats-tags" id="tags-<?php echo $author->id;?>">
                            <?php if ($author->tags) { ?>
    							<?php foreach ($author->tags as $tag) { ?>
    							<a class="btn btn-default" href="<?php echo $tag->getPermalink(); ?>">
    								<i class="fa fa-tag text-muted"></i> &nbsp; <?php echo $tag->getTitle(); ?>
    							</a>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_AUTHOR_DID_NOT_USE_ANY_TAGS_YET');?></div>
                            <?php } ?>
						</div>
                        <?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-users"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_AUTHORS_CURRENTLY'); ?>
		</div>
	<?php } ?>

	<?php if ($pagination) { ?>
	<div class="eb-pagination clearfix">
		<?php echo $pagination; ?>
	</div>
	<?php } ?>
</div>
