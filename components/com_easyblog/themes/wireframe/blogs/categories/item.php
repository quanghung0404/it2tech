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
<?php if ($this->params->get('category_header', true)) { ?>
<div class="eb-category">
	<div class="eb-category-profile">
		<?php if ($this->config->get('layout_categoryavatar', true) && $this->params->get('category_avatar', true)) { ?>
		<div class="col-cell eb-category-thumb">
			<a href="<?php echo $category->getPermalink();?>" class="eb-avatar">
				<img src="<?php echo $category->getAvatar();?>" class="eb-category-avatar" width="60" height="60" alt="<?php echo $category->getTitle();?>" />
			</a>
		</div>
		<?php } ?>

		<div class="col-cell eb-category-details">
			<?php if ($this->params->get('category_title', true)) { ?>
			<div class="eb-category-head">
				<h2 class="eb-category-name reset-heading">
					<a href="<?php echo $category->getPermalink();?>" class="text-inherit"><?php echo $category->getTitle();?></a>
				</h2>
			</div>
			<?php } ?>

			<div class="eb-category-subscribe spans-seperator">
				<?php if ($this->params->get('category_subscribe_email', true) && $this->config->get('main_categorysubscription')) { ?>
					<?php if (!$isCategorySubscribed) { ?>
						<span class="eb-category-subscription">
							<a href="javascript:void(0);" class="link-subscribe" data-blog-subscribe data-type="category" data-id="<?php echo $category->id;?>">
								<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_CATEGORY'); ?>
							</a>
						</span>
					<?php } else { ?>
						<span class="eb-category-subscription">
							<a href="javascript:void(0);" class="link-subscribe" data-blog-unsubscribe data-type="category" data-subscription-id="<?php echo $subscriptionId;?>" data-return="<?php echo base64_encode(JRequest::getURI());?>">
								<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_CATEGORY'); ?>
							</a>
						</span>
					<?php } ?>
				<?php } ?>

				<?php if ($this->params->get('category_subscribe_rss', true) && $this->config->get('main_rss') && $privacy->allowed) { ?>
					<span class="eb-category-rss">
						<a href="<?php echo $category->getRSS();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="link-rss">
							<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>
						</a>
					</span>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php if ($category->get('description') && $this->params->get('category_description', true)) { ?>
	<div class="eb-category-bio">
		<?php echo $category->get( 'description' ); ?>
	</div>
	<?php } ?>

	<?php if (!empty($category->nestedLink) && $this->params->get('category_subcategories', true)) { ?>
	<div class="eb-category-subs">
		<p><b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_SUBCATEGORIES'); ?></b></p>
		<?php echo $category->nestedLink; ?>
	</div>
	<?php } ?>
</div>
<?php } ?>

<div class="eb-posts eb-responsive" data-blog-posts>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-entries');?>

	<?php if ($this->my->guest && $category->private == 1) { ?>
		<div class="eb-empty">
			<i class="fa fa-info-circle"></i>
			<?php echo JText::_('COM_EASYBLOG_CATEGORIES_FOR_REGISTERED_USERS_ONLY');?>
		</div>
	<?php } ?>

	<?php if ($category->private == 2 && !$allowCat) { ?>
		<div class="eb-empty">
			<i class="fa fa-info-circle"></i>
			<?php echo JText::_('COM_EASYBLOG_CATEGORIES_NOT_ALLOWED');?>
		</div>
	<?php } ?>

	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>

			<!-- Determine if post custom fields should appear or not in category listings -->
			<?php if (!$this->params->get('category_post_customfields')) { ?>
				<?php $post->fields = '';?>
			<?php } ?>

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

	<!-- @module: easyblog-after-entries -->
	<?php echo EB::renderModule('easyblog-after-entries'); ?>
</div>

<?php if($pagination) {?>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<!-- Pagination items -->
	<?php echo $pagination;?>

	<!-- @module: easyblog-after-pagination -->
	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
