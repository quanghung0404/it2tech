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

if ($layout == 'horizontal') {
    echo '<div class="mod-grid mod-grid-' . $gridPath . '" style="width: ' . 100/$columnCount .'%">';
}
?>
<div class="mod-item">
    <div class="eb-mod-head mod-table align-middle">
        <?php if ($params->get('showavatar', true)) { ?>
            <div class="mod-cell cell-tight">
                <a href="<?php echo $post->getAuthor()->getProfileLink(); ?>" class="mod-avatar-sm mr-10">
                    <img src="<?php echo $post->getAuthor()->getAvatar(); ?>" width="50" height="50" />
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
                    <?php echo $post->getCreationDate()->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
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
        <?php if ($post->posttype == 'quote' && $params->get('showintro', '-1') != '-1') { ?>
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
            <div class="eb-mod-title">
                <a href="<?php echo $post->getPermalink(); ?>" class="eb-mod-media-title"><?php echo $post->title;?></a>
            </div>
        <?php } ?>

        <?php if (in_array($post->posttype, array('twitter', 'email', 'link'))) { ?>
            <div class="eb-mod-title">
                <a href="<?php echo $post->posttype == 'link'? $post->getAsset('link')->getValue() : $url; ?>" class="eb-mod-media-title"><?php echo $post->title;?></a>
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

    <?php if ($params->get('showratings', true) && $post->showRating) { ?>
        <div class="eb-rating">
            <?php echo EB::ratings()->html($post, 'ebmostpopular-' . $post->id . '-ratings', JText::_('MOD_EASYBLOGMOSTPOPULARPOST_RATEBLOG'), $disabled); ?>
        </div>
    <?php } ?>

    <div class="eb-mod-foot mod-muted mod-small">
        <!-- Source -->
    <?php if ($params->get('show_post_type_icon', true)) { ?>
        <div class="mod-cell pr-10">
            <i class="fa fa-align-left" style="font-size: 14px;"></i>
        </div>
    <?php } ?>

    <?php if ($params->get('showhits' , true)) { ?>
        <div class="mod-cell pr-10">
            <?php echo JText::sprintf('MOD_EASYBLOGMOSTPOPULARPOST_HITS', $post->hits);?>
        </div>
    <?php } ?>

    <?php if ($params->get('showcommentcount', 0)) { ?>
        <div class="mod-cell pr-10">
            <a href="<?php echo $post->getPermalink();?>">
                <?php echo EB::string()->getNoun('MOD_EASYBLOGMOSTPOPULARPOST_COMMENTS', $post->commentCount, true);?>
            </a>
        </div>
    <?php } ?>

    <?php if ($params->get('showreadmore', true)) { ?>
        <div class="mod-cell">
            <a href="<?php echo $post->getPermalink(); ?>"><?php echo JText::_('MOD_EASYBLOGMOSTPOPULARPOST_READMORE'); ?></a>
        </div>
    <?php } ?>
    </div>
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
