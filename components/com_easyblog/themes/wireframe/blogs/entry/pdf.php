<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
$url = rtrim( JURI::base() , '/' );
?>

<h1><a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a></h1>
<p class="meta-bottom">
<?php echo JText::_('COM_EASYBLOG_POSTED_ON'); ?> <?php echo $post->getCreationDate()->format($this->config->get('layout_shortdateformat', JText::_('DATE_FORMAT_LC1'))); ?>,
<?php echo JText::sprintf('COM_EASYBLOG_POSTED_BY_AUTHOR', $post->author->getProfileLink(), $post->author->getName()); ?>
<?php echo JText::sprintf('COM_EASYBLOG_IN', $post->category->getPermalink(), $this->escape($post->category->getTitle())); ?>
</p>
<?php echo $post->event->afterDisplayTitle; ?>
<?php echo $post->event->beforeDisplayContent; ?>
<?php echo $post->getContent(); ?>
<?php echo $post->event->afterDisplayContent; ?>
<p><?php echo $post->copyrights; ?></p>
<?php if ($post->tags) { ?>
<p>
    <?php echo JText::_('COM_EASYBLOG_TAGS'); ?>
    <?php foreach ($tags as $tag) { ?>
    <a href="<?php echo $tag->getPermalink();?>">
        <?php echo JText::_($tag->title);?>
    </a>
    <?php } ?>
</p>
<?php } ?>
