<?php
/**
 * @version		$Id: default.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div id="fpssContainer<?php echo $module->id; ?>" class="fpss-container fpss-template-fsd textEffectSlideRight">
	<div class="fpssTimerContainer">
		<div class="fpssTimer"></div>
	</div>
	<div class="slides-wrapper">
		<div class="slide-loading"></div>
		<div class="slides">
			<?php foreach($slides as $key => $slide): ?>
			<div class="slide">
				<a<?php echo $slide->target; ?> href="<?php echo $slide->link; ?>" class="slide-link">
					<span class="slide-read-more"><?php echo JText::_('FPSS_MORE'); ?></span>
					<span class="slide-image" style="background:url(<?php echo $slide->mainImage; ?>) no-repeat;">
						<img src="<?php echo $slide->mainImage; ?>" alt="<?php echo $slide->altTitle; ?>" />
					</span>
					<span class="fpss-clr">&nbsp;</span>
				</a>
				<span class="fpss-clr">&nbsp;</span>
				<?php if($slide->content): ?>
				<div class="slidetext">
					<?php if($slide->params->get('title')): ?>
					<h1><a<?php echo $slide->target; ?> href="<?php echo $slide->link; ?>"><?php echo $slide->title; ?></a></h1>
					<?php endif; ?>

					<?php if($slide->params->get('category') && $slide->category): ?>
					<h2><?php echo $slide->category; ?></h2>
					<?php endif; ?>

					<?php if($slide->params->get('tagline') && $slide->tagline): ?>
					<h3><?php echo $slide->tagline; ?></h3>
					<?php endif; ?>

					<?php if($slide->params->get('author') && $slide->author): ?>
					<h4><?php echo JText::_('FPSS_MOD_BY'); ?> <?php echo $slide->author; ?></h4>
					<?php endif; ?>

					<?php if($slide->params->get('text') && $slide->text): ?>
					<p><?php echo $slide->text; ?></p>
					<?php endif; ?>

					<?php if($slide->params->get('readmore') && $slide->link): ?>
					<a<?php echo $slide->target; ?> href="<?php echo $slide->link; ?>" class="fpssReadMore" title="<?php echo JText::_('FPSS_MOD_READ_MORE_ABOUT'); ?> <?php echo $slide->altTitle; ?>"><?php echo JText::_('FPSS_MORE'); ?></a>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="navigation-wrapper"<?php if($params->get('hideNavigation')): ?> style="display:none;"<?php endif; ?>>
		<div class="navigation-background"></div>
		<ul class="navigation">
			<?php foreach($slides as $key => $slide): ?>
			<li class="navigation-button">
				<a href="<?php echo $slide->link; ?>" title="<?php echo $slide->altTitle; ?>">
					<span class="navigation-thumbnail" style="background:url(<?php echo $slide->thumbnailImage; ?>) no-repeat;">&nbsp;</span>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>