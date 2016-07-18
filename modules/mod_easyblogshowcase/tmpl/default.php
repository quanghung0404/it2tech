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

<?php if ($config->get('main_ratings')) { ?>
<script type="text/javascript">
EasyBlog.require()
.script('ratings')
.done(function($) {
    $('#fd.mod-easyblogshowcase [data-rating-form]').implement(EasyBlog.Controller.Ratings);
});

</script>
<?php } ?>

<div id="fd" class="eb eb-mod st-1 eb-responsive mod-easyblogshowcase<?php echo $params->get('moduleclass_sfx'); ?>">
	<div class="eb-gallery" data-autoplay="<?php echo $autoplay;?>" data-interval="<?php echo $autoplayInterval;?>">
		<div class="eb-gallery-stage">
			<div class="eb-gallery-viewport">
				<?php foreach ($posts as $post) { ?><div class="eb-gallery-item"> <!--PLEASE KEEP THIS DOM THIS WAY TO REMOVE WHITESPACING-->
					<div class="eb-gallery-box">
						<?php if ($params->get('photo_show', true)) { ?>
							<?php if ($post->postCover) { ?>
							<div class="eb-gallery-thumb eb-mod-thumb is-<?php echo $post->postCoverLayout->alignment; ?>">
								<?php if (isset($post->postCoverLayout->layout->crop) && $post->postCoverLayout->layout->crop) { ?>
						            <a href="<?php echo $post->getPermalink();?>" class="eb-mod-image-cover"
						                style="
						                    background-image: url('<?php echo $post->postCover;?>') !important;
						                    width: <?php echo $post->postCoverLayout->layout->width;?>px;
						                    height: <?php echo $post->postCoverLayout->layout->height;?>px;"
						            ></a>
								<?php } else { ?>
						            <a href="<?php echo $post->getPermalink();?>" class="eb-mod-image"
						                style="width:<?php echo $post->postCoverLayout->layout->width;?>px;">
						                <img src="<?php echo $post->postCover;?>" alt="<?php echo $post->title;?>" />
						            </a>
								<?php } ?>
							</div>
							<?php } ?>
						<?php } ?>

						<div class="eb-gallery-body">
							<?php if ($params->get('authoravatar', true)) { ?>
								<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="eb-gallery-avatar mod-avatar">
									<img src="<?php echo $post->getAuthor()->getAvatar(); ?>" width="50" height="50" />
								</a>
							<?php } ?>

							<h3 class="eb-gallery-title">
								<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
							</h3>

							<div class="eb-gallery-meta">
								<?php if ($params->get('contentauthor', true)) { ?>
									<span>
										<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $post->getAuthor()->getName(); ?></a>
									</span>
								<?php } ?>

								<span>
									<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo $post->getPrimaryCategory()->title;?></a>
								</span>

								<?php if ($params->get('contentdate' , true)) { ?>
									<span>
										<?php echo $post->getCreationDate()->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
									</span>
								<?php } ?>
							</div>

							<div class="eb-gallery-content">
								<?php echo $post->content; ?>
							</div>

							<?php if ($params->get('showratings', true)) { ?>
							    <div class="eb-rating">
							        <?php echo EB::ratings()->html($post, 'ebmostshowcase-' . $post->id . '-ratings', JText::_('MOD_SHOWCASE_RATE_BLOG_ENTRY'), $disabled); ?>
							    </div>
							<?php } ?>

							<?php if ($params->get('showreadmore', true)) { ?>
								<div class="eb-gallery-more">
									<a href="<?php echo $post->getPermalink();?>"><?php echo JText::_('MOD_SHOWCASE_READ_MORE');?></a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div><?php } ?> <!--PLEASE KEEP THIS DOM THIS WAY TO REMOVE WHITESPACING-->
			</div>

			<?php if (count($posts) > 1) { ?>
			<div class="eb-gallery-buttons">
				<div class="eb-gallery-button eb-gallery-prev-button">
					<i class="fa fa-angle-left"></i>
				</div>
				<div class="eb-gallery-button eb-gallery-next-button">
					<i class="fa fa-angle-right"></i>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="eb-gallery-menu hidden">
			<?php $i = 0; ?>
			<?php foreach ($posts as $post) { ?>
				<div class="eb-gallery-menu-item <?php echo $i == 0 ? 'active' : '';?>"></div>
				<?php $i++;?>
			<?php } ?>
		</div>
	</div>
</div>
