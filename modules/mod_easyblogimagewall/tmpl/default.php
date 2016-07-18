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
<?php if ($posts) { ?>
<div id="fd" class="eb eb-mod mod-easyblogimagewall ezb-mod <?php echo $params->get('moduleclass_sfx') ?>">
	<?php $i = 1; ?>

	<div class="ezb-grids">
		<?php foreach ($posts as $post) { ?>
			<div class="ezb-grid" style="width: <?php echo 100 / $params->get('columns');?>%">
				<a class="ezb-card" href="<?php echo EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id); ?>" title="<?php echo $post->title; ?>" style="background-image: url('<?php echo $post->media; ?>');">
					
					<span><?php echo $post->title; ?></span>
				</a>
			</div>

			<?php if ($i % $params->get('columns', 4) == 0) { ?>
				<div class="clear"></div>
			<?php } ?>
			<?php $i++; ?>
		<?php } ?>
	</div>
</div>
<?php } ?>

