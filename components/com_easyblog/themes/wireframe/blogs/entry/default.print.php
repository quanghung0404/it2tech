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
<script type="text/javascript">
window.print();
</script>
<div id="blog-title">
	<h1><?php echo $post->title; ?></h1>
</div>

<div class="title-wrapper no-avatar">
	<div class="meta1">
		<div class="inner">
			<span class="post-date"><?php echo $post->getCreationDate()->format($this->config->get('layout_shortdateformat', JText::_('DATE_FORMAT_LC1'))); ?></span>

			<span class="post-category">
				<?php echo JText::sprintf('COM_EASYBLOG_POSTED_BY_AUTHOR', $post->author->getProfileLink(), $post->author->getName()); ?>
				<?php echo JText::sprintf('COM_EASYBLOG_IN', $post->category->getPermalink(), $this->escape($post->category->getTitle())); ?>
			</span>
		</div>
	</div>
</div>

<?php echo $post->event->afterDisplayTitle; ?>

<?php echo $post->event->beforeDisplayContent; ?>
<div class="post-content clearfix">
	<?php echo $post->getContent(EASYBLOG_VIEW_ENTRY); ?>
</div>
<?php echo $post->event->afterDisplayContent; ?>

<div class="post-copyright clearfix">
	<?php echo $post->copyrights; ?>
</div>

<?php if ($post->tags) { ?>
<div class="post-tags">
	<?php echo JText::_('COM_EASYBLOG_TAGS'); ?>

	<?php foreach ($post->tags as $tag) { ?>
	<a href="<?php echo $tag->getPermalink();?>">
		<span itemprop="keywords"><?php echo JText::_($tag->title);?></span>
	</a>
	<?php } ?>
</div>
<?php } ?>

<a rel="nofollow" onclick="window.print();" title="<?php echo JText::_('PRINT'); ?>" href="javascript: void(0)"><?php echo JText::_('PRINT'); ?></a>