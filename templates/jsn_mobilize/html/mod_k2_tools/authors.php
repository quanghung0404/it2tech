<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2AuthorsListBlock">
  <ul>
    <?php foreach ($authors as $author): ?>
    <li>
      <?php if ($params->get('authorAvatar')): ?>
      <a class="k2Avatar abAuthorAvatar" rel="author" href="<?php echo $author->link; ?>" title="<?php echo K2HelperUtilities::cleanHtml($author->name); ?>">
      	<img src="<?php echo $author->avatar; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($author->name); ?>" style="width:<?php echo $avatarWidth; ?>px;height:auto;" />
      </a>
      <?php endif; ?>

      <a class="abAuthorName" rel="author" href="<?php echo $author->link; ?>">
      	<?php echo $author->name; ?>

      	<?php if ($params->get('authorItemsCounter')): ?>
      	<span>(<?php echo $author->items; ?>)</span>
      	<?php endif; ?>
      </a>

      <?php if ($params->get('authorLatestItem')): ?>
      <a class="abAuthorLatestItem" href="<?php echo $author->latest->link; ?>" title="<?php echo K2HelperUtilities::cleanHtml($author->latest->title); ?>">
      	<?php echo $author->latest->title; ?>
	      <span class="abAuthorCommentsCount">
	      	(<?php echo $author->latest->numOfComments; ?> <?php if($author->latest->numOfComments=='1') echo JText::_('K2_MODK2TOOLS_COMMENT'); else echo JText::_('K2_MODK2TOOLS_COMMENTS'); ?>)
	      </span>
      </a>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
