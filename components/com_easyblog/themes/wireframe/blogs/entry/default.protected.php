<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div itemscope itemtype="http://schema.org/BlogPosting" data-blog-post>
	<div id="entry-<?php echo $post->id; ?>" class="eb-entry fd-cf" data-blog-posts-item data-id="<?php echo $post->id;?>">

        <?php if ($hasEntryTools || $hasAdminTools || $preview) { ?>
        <div class="eb-entry-tools row-table">
            <div class="col-cell">
                <?php echo $this->output('site/blogs/entry/tools', array('return' => $post->getPermalink(false))); ?>
            </div>

            <?php if (!$preview) { ?>
            <div class="col-cell cell-tight">
                <?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $post->getPermalink(false))); ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

		<!-- @module: easyblog-before-entry -->
		<?php echo EB::renderModule('easyblog-before-entry'); ?>

		<div class="eb-entry-head">
			<?php if ($this->params->get('show_title', true)) { ?>
			<h1 itemprop="name headline" id="title-<?php echo $post->id; ?>" class="eb-entry-title reset-heading <?php echo ($post->isFeatured()) ? ' featured-item' : '';?> "><?php echo $post->title; ?></h1>
			<?php } ?>

			<div class="eb-entry-meta text-muted">
				<?php if ($post->isFeatured()) { ?>
				<div class="eb-entry-featured">
					<i class="fa fa-star text-muted"></i>
					<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></span>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_date', true)) { ?>
				<div class="eb-entry-date">
					<i class="fa fa-clock-o"></i>
					<time class="eb-meta-date" itemprop="datePublished" content="<?php echo $post->getCreationDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
						<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>

				</div>
				<?php } ?>

				<?php if ($this->params->get('show_author', true)) { ?>
				<div class="eb-meta-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
					<i class="fa fa-pencil"></i>
					<span itemprop="name">
						<a href="<?php echo $post->author->getPermalink();?>" itemprop="url" rel="author"><?php echo $post->author->getName();?></a>
					</span>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_category', true)) { ?>
					<div class="eb-meta-category comma-seperator">
						<i class="fa fa-folder-open"></i>
						<?php foreach ($post->categories as $cat) { ?>
						<span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ($this->params->get('show_hits', true)) { ?>
				<div class="eb-meta-views">
					<i class="fa fa-eye"></i>
					<?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
				</div>
				<?php } ?>
			</div>
		</div>

		<div class="eb-entry-body clearfix">
			<div class="eb-entry-article clearfix" itemprop="articleBody" data-blog-content>
				<?php echo $this->output('site/blogs/tools/protected.form'); ?>
			</div>
		</div>
	</div>
</div>
