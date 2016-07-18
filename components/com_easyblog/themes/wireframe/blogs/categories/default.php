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
defined('_JEXEC') or die('Restricted access');
?>
<div class="eb-categories">
<?php if ($categories) { ?>
	<?php foreach ($categories as $category) { ?>
	<div class="eb-category">
		<div class="eb-category-profile row-table">
			<?php if ($config->params->get('category_avatar', true)) { ?>
			<div class="col-cell cell-tight eb-category-thumb">
				<a href="<?php echo $category->getPermalink(); ?>" class="eb-avatar">
					<img src="<?php echo $category->getAvatar();?>" align="top" class="eb-category-avatar" width="60" height="60" alt="<?php echo $category->getTitle();?>" />
				</a>
			</div>
			<?php } ?>

			<div class="col-cell eb-category-details">
				<?php if ($config->params->get('category_title', true)) { ?>
				<div class="eb-category-head">
					<h2 class="eb-category-name reset-heading">
						<a href="<?php echo $category->getPermalink();?>" class="text-inherit"><?php echo $category->getTitle();?></a>
					</h2>
				</div>
				<?php } ?>

				<div class="eb-category-subscribe spans-seperator">

					<?php if ((($category->private && $this->my->id != 0) || ($this->my->id == 0 && $this->config->get('main_allowguestsubscribe')) || !$this->my->guest) && $this->config->get('main_categorysubscription')) { ?>

						<?php if (!$category->isCategorySubscribed) { ?>			
								<span class="eb-category-subscription">
									<a href="javascript:void(0);" class="link-subscribe" data-blog-subscribe data-type="category" data-id="<?php echo $category->id;?>">
										<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>
									</a>
								</span>
						<?php } else { ?>
								<span class="eb-category-subscription">
									<a href="javascript:void(0);" class="link-subscribe" data-blog-unsubscribe data-subscription-id="<?php echo $category->subscriptionId;?>" data-return="<?php echo base64_encode(JRequest::getURI());?>">
										<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_CATEGORY'); ?>
									</a>
								</span>
						<?php } ?>
					<?php } ?>

					<?php if ($this->config->get('main_rss') && $config->params->get('category_rss', true)) { ?>
					<span class="eb-category-rss">
						<a href="<?php echo $category->getRssLink();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS', false); ?>" class="link-rss"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></a>
					</span>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php if ($config->params->get('category_description', true) && $category->description) { ?>
		<div class="eb-category-bio">
			<?php echo $category->description;?>
		</div>
		<?php } ?>

		<?php if (!empty($category->nestedLink) && $config->params->get('subcategories', true)) { ?>
		<div class="eb-category-subs">
			<p>
				<b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_SUBCATEGORIES'); ?></b>
			</p>

			<?php echo $category->nestedLink; ?>
		</div>
		<?php } ?>

		<?php if ($config->params->get('category_posts', true) || $config->params->get('category_authors', true)) { ?>
		<div class="eb-category-stats">

			<ul class="eb-stats-nav reset-list">
				<?php if ($config->params->get('category_posts', true)) { ?>
				<li class="active">
					<a class="btn btn-default btn-block" href="#posts-<?php echo $category->id; ?>" data-bp-toggle="tab">
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_POSTS');?>
						<b><?php echo $category->cnt; ?></b>
					</a>
				</li>
				<?php } ?>

				<?php if ($config->params->get('category_authors', true)) { ?>
				<li>
					<a class="btn btn-default btn-block" href="#authors-<?php echo $category->id; ?>" data-bp-toggle="tab">
						<?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACTIVE_BLOGGERS');?>
						<b><?php echo ($category->authors) ? count( $category->authors ) : '0' ;?></b>
					</a>
				</li>
				<?php } ?>
			</ul>

			<div class="eb-stats-content">
				<?php if ($config->params->get('category_posts', true)) { ?>
				<div class="tab-pane eb-stats-posts active eb-responsive" id="posts-<?php echo $category->id; ?>">
					<?php if ($category->blogs) { ?>
						<?php foreach ($category->blogs as $post) { ?>
						<div>
							<time><?php echo $post->getDisplayDate($post->category->getParam('listing_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC3'));?></time>

							<?php echo $post->getIcon('eb-post-type'); ?>

							<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
						</div>
						<?php } ?>

						<a href="<?php echo $category->getPermalink();?>" class="btn btn-default btn-block btn-show-all">
							<?php echo JText::_('COM_EASYBLOG_CATEGORIES_VIEW_ALL_POSTS');?> &nbsp;<i class="fa fa-chevron-right"></i>
						</a>
					<?php } else { ?>
						<div class="eb-empty">
							<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
						</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($config->params->get('category_authors', true)) { ?>
				<div class="tab-pane eb-labels eb-stats-authors" id="authors-<?php echo $category->id; ?>">
					<?php if ($category->authors) { ?>
						<?php $i = 0; ?>
						<?php foreach ($category->authors as $author) { ?>
							<div class="eb-stats-author row-table<?php echo $i >= $limit ? ' hide' : '';?>" data-author-item data-src="<?php echo $author->getAvatar(); ?>">
								<?php if ($this->config->get('layout_avatar')) { ?>
	                            <a class="col-cell" href="<?php echo $author->getProfileLink(); ?>" title="<?php echo $author->getName(); ?>" class="eb-avatar">
	                                <img
	                                	<?php if ($i <= $limit) { ?>
	                                    src="<?php echo $author->getAvatar(); ?>"
	                                    <?php } ?>
	                                    alt="<?php echo $this->html('string.escape', $author->getName()); ?>"
	                                    width="64"
	                                    height="64"
	                                    alt="<?php echo $author->getName(); ?>"
	                                />
	                            </a>
	                            <?php } ?>

	                            <div class="col-cell">
	                                <a href="<?php echo $author->getProfileLink(); ?>">
	                                    <b><?php echo $author->getName(); ?></b>
	                                </a>
	                                <div>
	                                	<?php echo $this->getNouns('COM_EASYBLOG_AUTHOR_POST_COUNT', $author->getTotalPosts(), true); ?>
	                                </div>
	                            </div>
	                        </div>
                        	<?php $i++; ?>
						<?php } ?>


						<?php if (count($category->authors) > $limit) { ?>
							<a href="javascript:void(0);" class="btn btn-block btn-default mt-10" data-show-all-authors><?php echo JText::sprintf('COM_EASYBLOG_SHOW_ALL_BLOGGERS', count($category->authors)); ?> &raquo;</a>
						<?php } ?>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
<?php } else { ?>
	<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND'); ?></div>
<?php } ?>

	<?php if ($pagination) { ?>
		<?php echo $pagination; ?>
	<?php } ?>
</div>
