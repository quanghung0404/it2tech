ALTER TABLE `#__imageshow_theme_profile` ADD `theme_style_name` CHAR(255) NOT NULL DEFAULT '' AFTER `theme_name`;

UPDATE `#__imageshow_theme_profile` SET `theme_style_name` = 'flash' WHERE `theme_name` = 'themeclassic' AND `theme_style_name` = '';