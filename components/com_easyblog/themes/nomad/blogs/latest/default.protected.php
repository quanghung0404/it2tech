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
<div itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting" class="eb-post" data-blog-posts-item data-id="<?php echo $post->id;?>">

	<div class="eb-post-content">
		<?php if ($this->config->get('layout_avatar') && $this->params->get('post_author_avatar', true)) { ?>
		<div class="eb-post-avatar">
			<?php if ($post->isTeamBlog() && $this->config->get('layout_teamavatar')) { ?>
			<a href="<?php echo $post->getBlogContribution()->getPermalink(); ?>" class="eb-avatar-team eb-avatar">
				<img src="<?php echo $post->getBlogContribution()->getAvatar();?>" width="50" height="50" alt="<?php echo $post->getBlogContribution()->getTitle();?>" />
			</a>
			<?php } ?>

			<a href="<?php echo $post->author->getProfileLink(); ?>" class="eb-avatar-single eb-avatar">
				<img src="<?php echo $post->author->getAvatar();?>" width="50" height="50" alt="<?php echo $post->author->getName();?>" />
			</a>
		</div>
		<?php } ?>

		<div class="eb-post-head">
			<?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $return)); ?>

			<!-- Quote type -->
			<?php if ($post->posttype == 'quote') { ?>
			<div class="eb-post-headline">
				<h2 itemprop="name headline" class="eb-post-title reset-heading">
					<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo nl2br($post->title);?></a>
				</h2>

				<div class="eb-post-headline-source">
					<?php echo $post->getContent(); ?>
				</div>
			</div>
			<?php } ?>

			<!-- Link type -->
			<?php if ($post->posttype == 'link') { ?>
			<?php $link = $post->getAsset('link')->getValue(); ?>
			<div class="eb-post-headline">
				<h2 itemprop="name headline" class="eb-placeholder-link-title eb-post-title reset-heading">
					<a href="<?php echo $post->getPermalink();?>"><?php echo nl2br($post->title);?></a>
				</h2>

				<div class="eb-post-headline-source">
					<a href="<?php echo $post->getAsset('link'); ?>" target="_blank">
						<?php echo EB::string()->htmlAnchorLink($link, $link); ?>
					</a>
				</div>
			</div>
			<?php } ?>

			<!-- Twitter type -->
			<?php if ($post->posttype == 'twitter') { ?>
			<?php $screen_name = $post->getAsset('screen_name')->getValue();
				  $created_at = EB::date($post->getAsset('created_at')->getValue(), true)->format(JText::_('DATE_FORMAT_LC'));
			?>
			<div class="eb-post-headline">
				<h2 itemprop="name headline" class="eb-post-title-tweet reset-heading">
					<?php echo $post->content;?>
				</h2>

				<?php if (!empty($screen_name) && !empty($created_at)) { ?>
				<div class="eb-post-headline-source">
						<?php echo '@'.$screen_name.' - '.$created_at; ?>
						&middot;
						<a href="<?php echo $post->getPermalink();?>">
							<?php echo JText::_('COM_EASYBLOG_LINK_TO_POST'); ?>
						</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<!-- Photo/Video/Standard type -->
			<?php if ((in_array($post->posttype, array('photo', 'standard', 'video', 'email'))) && $this->params->get('post_title', true)) { ?>
			<h2 itemprop="name headline" class="eb-post-title reset-heading">
				<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
			</h2>
			<?php } ?>

			<?php if ($this->params->get('post_date', true) || $this->params->get('post_author', true) || $this->params->get('post_category', true)) { ?>
			<div class="eb-post-meta text-muted">
				<?php if ($post->isFeatured) { ?>
				<div class="eb-post-featured">
					<!-- <i class="fa fa-star" data-original-title="" data-placement="bottom" data-eb-provide="tooltip"></i> -->
					<b><?php echo JText::_('COM_EASYBLOG_FEATURED');?></b>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_author', true)) { ?>
				<div class="eb-post-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
					<i class="fa fa-user"></i>
					<span itemprop="name">
						<a href="<?php echo $post->author->getProfileLink();?>" itemprop="url" rel="author"><?php echo $post->author->getName();?></a>
					</span>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_date', true)) { ?>
				<div class="eb-post-date">
					<i class="fa fa-clock-o"></i>
					<time class="eb-meta-date" itemprop="datePublished" content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
						<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_category', true) && $post->categories) { ?>
				<div class="eb-post-category comma-seperator">
					<i class="fa fa-folder-open"></i>
					<?php foreach ($post->categories as $category) { ?>
					<span>
						<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
					</span>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>

	    <div class="eb-post-protected">
			<?php echo $this->output('site/blogs/tools/protected.form', array('post' => $post)); ?>
	    </div>

		
	</div>
</div>
