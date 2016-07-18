ALTER TABLE `#__imageshow_theme_grid`
ADD COLUMN `image_source` char(150) DEFAULT 'thumbnail',
ADD COLUMN `show_caption` char(150) DEFAULT 'yes',
ADD COLUMN `caption_show_description` char(150) DEFAULT 'yes',
ADD COLUMN `show_close` char(150) DEFAULT 'yes',
ADD COLUMN `show_thumbs` char(150) DEFAULT 'yes',
ADD COLUMN `click_action` char(150) DEFAULT 'show_original_image',
ADD COLUMN `open_link_in` char(150) DEFAULT 'current_browser';
