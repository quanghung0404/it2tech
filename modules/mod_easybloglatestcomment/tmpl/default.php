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
<div id="fd" class="eb eb-mod mod_easybloglatestcomment<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if ($comments) { ?>
	<?php foreach ($comments as $comment) { ?>
	<div class="eb-mod-item">
		<div class="eb-mod-head mod-table cell-top">
			<?php if ($params->get('showavatar')) { ?>
				<div class="mod-cell cell-tight">
					<a href="#" class="mod-avatar mr-10">
						<img src="<?php echo $comment->author->getAvatar();?>" width="50" height="50" />
					</a>
				</div>
			<?php } ?>

			<div class="mod-cell">
				<?php if ($params->get('showauthor')) { ?>
					<strong><?php echo $comment->created_by == 0 ? $comment->name : '<a href="' . EBR::_( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $comment->author->id) . '" class="eb-mod-media-title">' . $comment->author->getName() . '</a>';?></a></strong>
				<?php } ?>

				<?php if ($params->get('showauthor') && $params->get('showtitle')) { ?>
					<i class="fa fa-chevron-right mod-xsmall mod-muted"></i>
				<?php } ?>

				<?php if ($params->get('showtitle')) { ?>
					<strong><a href="<?php echo EBR::_('index.php?option=com_easyblog&view=entry&id=' . $comment->post_id); ?>" class="eb-mod-media-title"> <?php echo $comment->blog_title; ?></a></strong>
				<?php } ?>	

				<div class="mod-muted mod-small">
					<?php echo $comment->dateString; ?>
				</div>

				<div class="eb-mod-body">
					<?php $text = strip_tags(EB::comment()->parseBBCode( $comment->comment )); ?>
					<?php echo JString::strlen($text) > $maxCharacter? JString::substr($text, 0, $maxCharacter) . '...' : $text; ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<?php } else { ?>
	<div><?php echo JText::_('MOD_EASYBLOGLATESTCOMMENT_NO_POST'); ?></div>
<?php } ?>
</div>
