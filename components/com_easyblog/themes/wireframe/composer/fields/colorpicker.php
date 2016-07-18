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

$attrs = '';
if (isset($attributes)) $attrs .= ' ' . $attributes;
?>
<div class="eb-composer-field eb-colorpicker colorpicker<?php echo $classname; ?>" data-type="colorpicker" data-eb-colorpicker<?php echo $attrs; ?>>

    <div class="eb-colorpicker-header row-table">
        <div class="col-cell">
            <?php echo $this->output('site/composer/fields/checkbox', array('classname' => 'eb-colorpicker-toggle', 'attributes' => 'data-eb-colorpicker-toggle')); ?>
        </div>
        <div class="col-cell">
            <input class="colorpicker-hex-input form-control" maxlength="7" size="7" type="text">
            <div class="colorpicker-preview"></div>
        </div>
    </div>

    <div class="colorpicker-hsb-panel">
        <div class="colorpicker-sb-panel">
            <div class="colorpicker-b-overlay"></div>
            <div class="colorpicker-s-overlay"></div>
            <div class="colorpicker-sb-handle"></div>
        </div>
        <div class="colorpicker-h-panel">
            <div class="colorpicker-h-handle"></div>
        </div>
    </div>

</div>