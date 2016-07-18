ALTER TABLE `#__imageshow_theme_grid`
ADD COLUMN `auto_play` char(150) DEFAULT 'no',
ADD COLUMN `slide_timing` char(150) DEFAULT '3',
ADD COLUMN `item_per_page` char(150) DEFAULT '5',
ADD COLUMN `navigation_type` char(150) DEFAULT 'show_all';