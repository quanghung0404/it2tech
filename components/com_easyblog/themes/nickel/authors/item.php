<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($author->custom_css && $author->getAcl()->get('custom_css')) { ?>
<style type="text/css">
<?php echo $author->custom_css;?>
</style>
<?php } ?>

<?php if ($this->params->get('author_header', true)) { ?>
<div class="eb-author" data-author-item data-id="<?php echo $author->id;?>">

	<div class="eb-authors-head row-table">
		<div class="col-cell cell-tight">
			<?php if ($this->config->get('layout_avatar') && $this->params->get('author_avatar', true)) { ?>
			<a href="<?php echo $author->getProfileLink();?>">
				<img src="<?php echo $author->getAvatar(); ?>" class="eb-authors-avatar" width="60" height="60" alt="<?php echo $author->getName(); ?>" />
			</a>
			<?php } ?>
		</div>
		<div class="col-cell">
			<?php if ($this->params->get('author_name', true)) { ?>
			<h2 class="eb-authors-name reset-heading">
				<a href="<?php echo $author->getProfileLink();?>" class="text-inherit"><?php echo $author->getName();?></a>
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

				<?php if ($this->config->get('main_bloggersubscription')) { ?>
					<?php if (!$isBloggerSubscribed) { ?>
					<span>
						<a href="javascript:void(0);" data-blog-subscribe data-type="blogger" data-id="<?php echo $author->id;?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER');?></a>
					</span>
					<?php } else { ?>
					<span>
						<a href="javascript:void(0);" data-blog-unsubscribe data-type="blogger" data-id="<?php echo $author->id;?>" data-email="<?php echo $this->my->email; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_BLOGGER');?></a>
					</span>
					<?php } ?>
				<?php } ?>

				<?php if ($this->config->get('main_rss') && $this->acl->get('allow_subscription_rss')) { ?>
				<span>
					<a href="<?php echo $author->getRssLink();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>">
						<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>
					</a>
				</span>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php if ($this->params->get('author_bio', true)) { ?>
		<div class="eb-authors-bio">
			<?php echo $author->getBiography(false, true);?>
		</div>
	<?php } ?>
</div>
<?php } ?>

<div class="eb-posts eb-masonry eb-responsive" data-blog-posts>
	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>
			<?php if (!EB::isSiteAdmin() && $this->config->get('main_password_protect') && !empty($post->blogpassword) && !$post->verifyPassword()) { ?>
				<?php echo $this->output('site/blogs/latest/default.protected', array('post' => $post)); ?>
			<?php } else { ?>
				<?php echo $this->output('site/blogs/latest/default.main', array('post' => $post)); ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-info-circle"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
		</div>
	<?php } ?>
</div>


<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination;?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
