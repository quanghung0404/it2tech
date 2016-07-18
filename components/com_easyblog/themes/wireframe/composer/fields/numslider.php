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

if (!isset($container)) $container = '';
if (!isset($label)) $label = '';
if (!isset($value)) $value = '100';
if (!isset($toggle)) $toggle = true;
if (!isset($selected)) $selected = false;
if (!isset($input)) $input = true;

$unitPresets = array(
    'pixel' => array(
        'title' => 'COM_EASYBLOG_PIXEL',
        'type'  => 'pixel',
        'unit'  => 'px'
    ),
    'percent' => array(
        'title' => 'COM_EASYBLOG_PERCENT',
        'type'  => 'percent',
        'unit'  => '%'
    )
);

if (!isset($units)) {
    $units = array('pixel', 'percent');
}

$hasUnits = is_array($units) && count($units) > 0;

if ($hasUnits) {

    $normalizedUnits = array();
    foreach ($units as $unit) {
        $normalizedUnits[] = is_string($unit) ? $unitPresets[$unit] : $unit;
    }
    $units = $normalizedUnits;

    // Use the first unit as default unit
    if (!isset($defaultUnit)) $defaultUnit = $units[0]['type'];
}

$attrs = '';
if (isset($name)) $attrs .= ' data-name="' . $name . '"';
if (isset($attributes)) $attrs .= ' ' . $attributes;
?>
<div class="eb-composer-field eb-numslider<?php echo $classname; ?>" data-type="numslider" data-eb-numslider<?php echo $attrs; ?>>

    <div class="row-table eb-composer-fieldrow" <?php echo $container; ?>>

        <?php if ($toggle) { ?>
        <div class="col-cell">
            <?php echo $this->output('site/composer/fields/checkbox', array('classname' => 'eb-numslider-toggle', 'attributes' => 'data-eb-numslider-toggle')); ?>
        </div>
        <?php } ?>

        <?php if (!empty($label)) { ?>
        <div class="eb-numslider-label col-cell eb-composer-fieldrow-label" data-eb-numslider-label>
            <span class="eb-numslider-label-caption" data-eb-numslider-label-caption><?php echo $label; ?></span>
        </div>
        <?php }?>

        <div class="eb-numslider-slider col-cell">
            <div class="eb-numslider-widget" data-eb-numslider-widget></div>
        </div>

        <?php if ($input) { ?>
        <div class="eb-numslider-value col-cell" data-eb-numslider-value>
            <div class="input-group">
                <input type="text" class="eb-numslider-input form-control" value="<?php echo $value; ?>" data-eb-numslider-input />
                <?php if ($hasUnits) { ?>
                <div class="input-group-btn eb-numslider-units" data-eb-numslider-units>
                    <?php foreach ($units as $unit) { ?>
                        <?php if ($unit['type']==$defaultUnit) { ?>
                        <button type="button" class="btn btn-default dropdown-toggle_" data-bp-toggle="dropdown" data-eb-numslider-unit-toggle>
                            <b class="eb-numslider-current-unit" data-eb-numslider-current-unit><?php echo $unit['unit']; ?></b>
                            <span class="caret"></span>
                        </button>
                        <?php } ?>
                    <?php } ?>

                    <ul class="dropdown-menu">
                        <?php foreach ($units as $unit) { ?>
                        <li class="<?php echo ($unit['type']==$defaultUnit) ? 'active' : ''; ?>" data-eb-numslider-unit data-type="<?php echo $unit['type']; ?>" data-unit="<?php echo $unit['unit']; ?>">
                            <a href="javascript:void(0);" title="<?php echo JText::_($unit['title']); ?>"><?php echo $unit['unit']; ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

    </div>
</div>