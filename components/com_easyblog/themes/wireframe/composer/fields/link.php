<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (!isset($classname)) {
    $classname = '';
} else {
    $classname = ' ' . $classname;
}
if (!isset($url)) $url = '';
if (!isset($title)) $title = '';
if (!isset($preview)) $preview = '';
if (!isset($openInNewPage)) $openInNewPage = false;
?>
<div class="eb-composer-field eb-link eb-link-item<?php echo $classname; ?>" data-type="link" data-eb-link data-eb-link-item>

    <div class="eb-link-preview" data-eb-link-preview>
        <i class="fa fa-link"></i>
        <span class="eb-link-preview-caption" data-eb-link-preview-caption><?php echo $preview; ?></span>
    </div>

    <div class="eb-link-input">
        <input class="eb-link-url-field form-control" type="text" value="<?php echo $url; ?>" placeholder="<?php echo JText::_('http://'); ?>" data-eb-link-url-field>
        <textarea class="eb-link-title-field form-control" placeholder="<?php echo JText::_('Enter link description'); ?>" data-eb-link-title-field><?php echo $title; ?></textarea>
    </div>

    <div class="eb-link-actions">
        <?php echo $this->output('site/composer/fields/checkbox', array(
            'classname' => 'eb-link-blank-option',
            'attributes' => 'data-eb-link-blank-option',
            'label' => JText::_('COM_EASYBLOG_COMPOSER_OPEN_IN_NEW_PAGE'),
            'checked' => $openInNewPage
        )); ?>
        <button type="button" class="btn btn-danger btn-xs eb-link-remove-button" data-eb-link-remove-button>
            <i class="fa fa-close"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_REMOVE'); ?>
        </button>
    </div>
</div>