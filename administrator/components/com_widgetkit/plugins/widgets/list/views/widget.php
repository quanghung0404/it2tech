<?php

use YOOtheme\Widgetkit\Content\Content;

// List
$list = 'uk-list';
switch ($settings['list']) {
    case 'line':
    case 'striped':
    case 'space':
        $list .= ' uk-list-' . $settings['list'];
        break;
}

// Media Align
$media_align = ($settings['media_align'] == 'right') ? 'uk-margin-left uk-flex-order-last' : 'uk-margin-right';

// Content Align
$content_align  = $settings['content_align'] ? 'uk-flex-middle' : '';

// Media Border
$border = ($settings['media_border'] != 'none') ? 'uk-border-' . $settings['media_border'] : '';

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

?>

<ul class="<?php echo $list; ?> <?php echo $settings['class']; ?>">

<?php foreach ($items as $i => $item) :

        // Media Type
        $attrs  = array('class' => '');
        $width  = $item['media.width'];
        $height = $item['media.height'];

        if ($item->type('media') == 'image') {
            $attrs['alt'] = strip_tags($item['title']);

            $attrs['class'] .= ($border) ? $border : '';

            $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
            $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
        }

        if ($item->type('media') == 'video') {
            $attrs['class'] = 'uk-responsive-width';
            $attrs['controls'] = true;
        }

        if ($item->type('media') == 'iframe') {
            $attrs['class'] = 'uk-responsive-width';
        }

        $attrs['width']  = ($width) ? $width : '';
        $attrs['height'] = ($height) ? $height : '';

        if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
            $media = $item->thumbnail('media', $width, $height, $attrs);
        } else {
            $media = $item->media('media', $attrs);
        }

        // Title
        $title = ($settings['title'] == 'title') ? $item['title'] : $item['content'];

        if ($settings['title_truncate']) {
            $title = Content::truncate($title, $settings['title_truncate']);
        }

        switch ($settings['title_size']) {
            case 'h1':
                $title = '<h1 class="uk-margin-remove">' . $title . '</h1>';
                break;
            case 'h1':
                $title = '<h2 class="uk-margin-remove">' . $title . '</h2>';
                break;
            case 'h3':
                $title = '<h3 class="uk-margin-remove">' . $title . '</h3>';
                break;
        }

        // Link Color
        $link_color = ($settings['link_color'] != 'link') ? 'uk-link-' . $settings['link_color'] : '';

    ?>

    <li>

        <?php if ($item['link'] && $settings['link']) : ?>
        <a class="uk-display-block <?php echo $link_color; ?>" href="<?php echo $item->escape('link') ?>"<?php echo $link_target; ?>>
        <?php endif; ?>

            <?php if ($item['media'] && $settings['media']) : ?>
            <div class="uk-flex <?php echo $content_align; ?>">
                <div class="<?php echo $media_align; ?>">
                    <?php echo $media; ?>
                </div>
                <div class="uk-flex-item-1">
            <?php endif; ?>

                <?php echo $title; ?>

            <?php if ($item['media'] && $settings['media']) : ?>
                </div>
            </div>
            <?php endif; ?>

        <?php if ($item['link'] && $settings['link']) : ?>
        </a>
        <?php endif; ?>

    </li>

<?php endforeach; ?>

</ul>
