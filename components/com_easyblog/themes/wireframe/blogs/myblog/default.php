<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-author" data-author-item data-id="<?php echo $author->id;?>">

	<div class="eb-authors-head row-table">
		<div class="col-cell cell-tight">
			<?php if ($this->config->get('layout_avatar') && $this->params->get('author_avatar', true)) { ?>
			<a href="<?php echo $author->getPermalink();?>">
				<img src="<?php echo $author->getAvatar(); ?>" class="eb-authors-avatar" width="60" height="60" alt="<?php echo $author->getName(); ?>" />
			</a>
			<?php } ?>
		</div>
		<div class="col-cell">
			<?php if ($this->params->get('author_name', true)) { ?>
			<h2 class="eb-authors-name reset-heading">
				<a href="<?php echo $author->getPermalink();?>" class="text-inherit"><?php echo $author->getName();?></a>
				<small class="eb-authors-featured eb-star-featured<?php echo !$author->isFeatured() ? ' hide' : '';?>" data-featured-tag data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>">
					<i class="fa fa-star"></i>
				</small>
			</h2>
			<?php } ?>

			<div class="eb-authors-subscribe spans-seperator">
				<?php if ($author->getTwitterLink() && $this->params->get('author_twitter', true)) { ?>
				<span>
					<a href="<?php echo $author->getTwitterLink(); ?>" title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?>">
						<i class="fa fa-twitter-square"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_FOLLOW_ME'); ?>
					</a>
				</span>
				<?php } ?>

				<?php if (EB::friends()->hasIntegrations($author)) { ?>
                <span>
                    <?php echo EB::friends()->html($author);?>
                </span>
                <?php } ?>

                <?php if (EB::messaging()->hasMessaging($author->id)) { ?>
                <span>
                    <?php echo EB::messaging()->html($author);?>
                </span>
                <?php } ?>

				<?php if ($author->getWebsite() && $this->params->get('author_website', true)) { ?>
				<span>
					<a href="<?php echo $author->getWebsite();?>" target="_blank">
						<i class="fa fa-globe"></i>&nbsp; <?php echo $author->getWebsite();?>
					</a>
				</span>
				<?php } ?>
				
			</div>
		</div>
	</div>

	<div class="eb-authors-bio">
		<?php if ($this->params->get('author_bio', true)) { ?>
			<?php echo $author->getBiography();?>
		<?php } ?>
	</div>
</div>

<!-- Post listings begins here -->
<div itemscope itemtype="http://schema.org/Blog" class="eb-posts eb-responsive" data-blog-posts>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-entries');?>

	<?php if ($posts) { ?>
		<?php foreach ($posts as $blog) { ?>
			<?php if (!EB::isSiteAdmin() && $this->config->get('main_password_protect') && !empty($blog->blogpassword)) { ?>
				<!-- Password protected theme files -->
				<?php echo $this->output('site/blogs/latest/default.protected', array('post' => $blog)); ?>
			<?php } else { ?>
				<?php echo $this->output('site/blogs/latest/default.main', array('post' => $blog)); ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-info-circle"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
		</div>
	<?php } ?>

	<!-- @module: easyblog-after-entries -->
	<?php echo EB::renderModule('easyblog-after-entries'); ?>
</div>

<?php if($pagination) {?>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<!-- Pagination items -->
	<?php echo $pagination->getPagesLinks();?>

	<!-- @module: easyblog-after-pagination -->
	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
