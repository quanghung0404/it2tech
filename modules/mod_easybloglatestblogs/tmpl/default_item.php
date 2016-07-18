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
<?php if ($layout == 'horizontal') { ?>
<div class="mod-grid mod-grid-<?php echo $gridPath;?>" style="width:<?php echo (100 / $columnCount);?>%;">
<?php } ?>

<div class="mod-item">
	<div class="eb-mod-head mod-table align-middle">
		<?php if ($params->get('showavatar', true) && !$bloggerLayout) { ?>
			<div class="mod-cell cell-tight">
				<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="mod-avatar-sm mr-10">
					<img src="<?php echo $post->getAuthor()->getAvatar(); ?>" width="50" height="50" alt="<?php echo $post->getAuthor()->getName();?>" />
				</a>
			</div>
		<?php } ?>

		<?php if ($params->get('showtavatar', true) && $post->isTeamBlog()) { ?>
			<div class="mod-cell cell-tight">
				<a href="<?php echo $post->getBlogContribution()->getPermalink(); ?>" class="mod-avatar-sm mr-10">
					<img src="<?php echo $post->getBlogContribution()->getAvatar(); ?>" width="50" height="50" alt="<?php echo $post->getAuthor()->getName();?>" />
				</a>
			</div>
		<?php } ?>

		<div class="mod-cell">

			<?php if ($params->get('showauthor', true)) { ?>
				<strong>
					<a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $post->getAuthor()->getName(); ?></a>
				</strong>
			<?php } ?>

			<?php if ($params->get('showdate' , true)) { ?>
				<div class="mod-muted mod-small mod-fit">
					<?php echo $post->getCreationDate(true)->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php
		// limit content height using .eb-mod-context
		if ($layout == 'horizontal') {
	 ?>
	<div class="eb-mod-context">
	<?php } ?>

		<?php if ($post->posttype == 'quote') { ?>
			<div class="eb-mod-hold-quote eb-mod-quote">
				<a href="<?php echo $post->getPermalink(); ?>" class="eb-mod-media-title"><?php echo nl2br($post->title);?></a>
				<div><?php echo  $post->content; ?></div>
			</div>
		<?php } ?>

		<?php if ($post->posttype == 'video') { ?>
			<?php if( !empty( $post->videos) && $params->get( 'video_show' , 1 ) ){ ?>
				<div class="eb-mod-hold">
				<?php foreach( $post->videos as $video ){ ?>
					<div class="eb-mod-hold-video eb-mod-responsive-video">
					<?php echo $video->html; ?>

					</div>
				<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if (in_array($post->posttype, array('twitter', 'email', 'link'))) { ?>
			<div class="eb-mod-title">
				<a href="<?php echo $post->posttype == 'link'? $post->getAsset('link')->getValue() : $post->getPermalink(); ?>" class="eb-mod-media-title"><?php echo $post->title;?></a>
			</div>
		<?php } else { ?>

			<?php if ($params->get('photo_show', true)) { ?>

				<?php
				$postCover = '';

				if ($post->hasImage()) {
					$postCover = $post->getImage($photoSize);
				}

				if (!$post->hasImage() && $params->get('photo_legacy', true)) {
					$postCover = $post->getContentImage();
				}

				if (!$postCover && $params->get('show_photo_placeholder', false)) {
					$postCover = $post->getImage($photoSize, true);
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

		<?php } ?>

		<?php if ($params->get('showcategory')) { ?>
			<?php foreach ($post->getCategories() as $category) { ?>
		        <div class="mod-post-type">
		             <a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle(); ?></a>
		        </div>
		     <?php } ?>
		<?php } ?>

		<?php if ($post->posttype != 'quote' && $params->get('showintro', '-1') != '-1') { ?>
			<div class="eb-mod-body">
			<?php if ($post->protect) { ?>
				<?php echo $post->content; ?>
			<?php } ?>

			<?php if (!$post->protect) { ?>
				<?php echo $post->summary; ?>
			<?php } ?>
			</div>
		<?php } ?>
	<?php
		// limit content height using .eb-mod-context
		if ($layout == 'horizontal') {
	 ?>
	</div>
	<?php } ?>

	<?php if ($config->get('main_ratings') && $params->get('showratings', true) && $post->showRating) { ?>
		<div class="eb-rating">
			<?php echo EB::ratings()->html($post, 'eblatestblogs-' . $post->id . '-ratings', JText::_('MOD_LATESTBLOGS_RATEBLOG'), $disabled); ?>
		</div>
	<?php } ?>

	<?php if ($params->get('showhits' , true) || $params->get('showcommentcount', 0) || $params->get('showreadmore', true)) { ?>
	<div class="eb-mod-foot mod-muted mod-small">

	<?php if ($params->get('showhits' , true)) { ?>
		<div class="mod-cell pr-10">
			<?php echo $post->hits;?> <?php echo JText::_('MOD_LATESTBLOGS_HITS');?>
		</div>
	<?php } ?>

	<?php if ($params->get('showcommentcount', 0)) { ?>
		<div class="mod-cell pr-10">
			<a href="<?php echo $post->getPermalink();?>">
				<?php echo EB::string()->getNoun('MOD_LATESTBLOGS_COMMENT_COUNT', EB::comment()->getCommentCount($post), true);?>
			</a>
		</div>
	<?php } ?>

	<?php if ($params->get('showreadmore', true)) { ?>
		<div class="mod-cell">
			<a href="<?php echo $post->getPermalink(); ?>"><?php echo JText::_('MOD_LATESTBLOGS_READMORE'); ?></a>
		</div>
	<?php } ?>
	</div>
	<?php } ?>
</div>
<?php
if ($layout == 'horizontal') {
	echo '</div>';
}
if ($gridPath<$columnCount) {
	$gridPath++;
} else {
	$gridPath = 1;
}
?>
