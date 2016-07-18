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
<?php if ($config->get('main_ratings')) { ?>
<script type="text/javascript">
EasyBlog.require()
.script('ratings')
.done(function($) {

	$('#fd.mod_easyblogrelatedpost [data-rating-form]').implement(EasyBlog.Controller.Ratings);
});

</script>
<?php } ?>
<div id="fd" class="eb eb-mod mod_easyblogrelatedpost<?php echo $params->get('moduleclass_sfx'); ?>">
	<!-- Entries -->
	<?php if ($posts) { ?>
	<div class="eb-mod">
		<?php foreach ($posts as $post) { ?>
			<div class="eb-mod-item">
				<!-- header -->
				<div class="eb-mod-head mod-table align-middle">
					<?php if ($params->get('showavatar', true)) { ?>
						<a href="<?php echo $post->getAuthor()->getPermalink(); ?>" class="mod-cell cell-tight pr-10">
							<img src="<?php echo $post->getAuthor()->getAvatar();?>" width="50" height="50">
						</a>
					<?php } ?>
					<div class="mod-cell">
						<?php require(JModuleHelper::getLayoutPath('mod_easyblogrelatedpost', 'source')); ?>
					</div>
				</div>

				<?php if ($params->get('photo_show', true)) { ?>
					<?php
					$postCover = '';

					if ($post->hasImage()) {
						$postCover = $post->getImage($photoSize);
					}

					if (!$post->hasImage() && $params->get('photo_legacy', true)) {
						$postCover = $post->getContentImage();
					}
					?>

					<?php if ($postCover) { ?>
					<div class="eb-mod-thumb<?php if ($photoAlignment) { echo " is-" . $photoAlignment; } ?> <?php if (isset($photoLayout->full) && $photoLayout->full) { echo "is-full"; } ?>">
						<?php if (isset($photoLayout->crop) && $photoLayout->crop) { ?>
				            <a href="<?php echo $post->getPermalink();?>" class="eb-mod-image-cover"
				                style="
				                    background-image: url('<?php echo $postCover;?>');
				                    <?php if (isset($photoLayout->full) && $photoLayout->full) { ?>
				                    width: 100%;
				                    <?php } else { ?>
				                    width: <?php echo $photoLayout->width;?>px;
				                    <?php } ?>
				                    height: <?php echo $photoLayout->height;?>px;"
				            ></a>
						<?php } else { ?>
				            <a href="<?php echo $post->getPermalink();?>" class="eb-mod-image"
				                style="
				                    <?php if (isset($photoLayout->full) && $photoLayout->full) { ?>
				                    width: 100%;
				                    <?php } else { ?>
				                    width: <?php echo (isset($photoLayout->width)) ? $photoLayout->width : '260';?>px;
				                    <?php } ?>"
				            >
				                <img src="<?php echo $postCover;?>" alt="<?php echo $post->title;?>" />
				            </a>
						<?php } ?>
					</div>
					<?php } ?>

				<?php } ?>

				<div class="eb-mod-title">
					<a href="<?php echo $post->getPermalink(); ?>" class="eb-mod-media-title"><?php echo $post->title;?></a>
				</div>

				<?php if ($params->get('showcategory')) { ?>
					<?php foreach ($post->getCategories() as $category) { ?>
		                <div class="mod-post-type">
		                    <a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle(); ?></a>
		                </div>
		            <?php } ?>
				<?php } ?>

				<?php if ($params->get('showintro', '-1') != '-1') { ?>
				<div class="eb-mod-body">

					<?php if ($post->protect) { ?>
						<?php echo $post->content; ?>
					<?php } ?>

					<?php if (!$post->protect) { ?>
						<?php echo $post->summary; ?>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($params->get('showhits' , true) || $params->get('showcommentcount', false) || $params->get('showreadmore', true)) { ?>
				<div class="eb-mod-foot mod-muted mod-small">
					<?php if ($params->get('showhits' , true)) { ?>
						<span><?php echo $post->hits;?> <?php echo JText::_( 'MOD_EASYBLOGRELATED_HITS' );?></span>
					<?php } ?>

					<?php if ($params->get('showcommentcount', false)) { ?>
						<span><a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->commentCount;?> <?php echo JText::_( 'MOD_EASYBLOGRELATED_COMMENTS' ); ?></a></span>
					<?php } ?>

					<?php if( $params->get('showreadmore', true)) { ?>
						<span><a href="<?php echo $post->getPermalink(); ?>"><?php echo JText::_('MOD_EASYBLOGRELATED_READMORE'); ?></a></span>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($params->get('showratings', true) && $post->showRating) { ?>
					<div class="eb-rating">
						<?php echo EB::ratings()->html($post, 'ebrelatedpost-' . $post->id . '-ratings', JText::_('MOD_EASYBLOGRELATED_RATEBLOG'), $disabled); ?>
					</div>
				<?php } ?>

			</div>
		<?php } ?>
	</div>

	<?php } else { ?>
			<?php echo JText::_( 'MOD_EASYBLOGRELATED_NO_POST'); ?>
	<?php } ?>
</div>
