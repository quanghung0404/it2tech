<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();
$jsnUtils   = JSNTplUtils::getInstance();

?>
<?php if ($jsnUtils->isJoomla3()): ?>
<div class="items-more">
<ul class="nav nav-tabs nav-stacked">
<?php else : ?>
<h2><?php echo JText::_('COM_CONTENT_MORE_ARTICLES'); ?></h2>
<ul>
<?php endif; ?>
<?php
	foreach ($this->link_items as &$item) :
?>
	<li>
		<a class="blogsection" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
			<?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ul>
<?php if ($jsnUtils->isJoomla3()): ?>
</div><?php endif; ?>
