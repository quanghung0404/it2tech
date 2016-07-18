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
}

$actions = array(
    'bold' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_BOLD',
        'icon'   => 'fa fa-bold',
        'format' => 'bold'
    ),

    'italic' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_ITALIC',
        'icon'   => 'fa fa-italic',
        'format' => 'italic'
    ),

    'underline' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_UNDERLINE',
        'icon'   => 'fa fa-underline',
        'format' => 'underline'
    ),

    'strikethrough' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_STRIKETHROUGH',
        'icon'   => 'fa fa-strikethrough',
        'format' => 'strikethrough'
    ),

    'code' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_CODE',
        'icon'   => 'fa fa-code',
        'format' => 'code'
    ),

    'subscript' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_SUBSCRIPT',
        'icon'   => 'fa fa-subscript',
        'format' => 'subscript'
    ),

    'superscript' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_FONT_SUPERSCRIPT',
        'icon'   => 'fa fa-superscript',
        'format' => 'superscript'
    ),

    'alignleft' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_LEFT',
        'icon'   => 'fa fa-align-left',
        'format' => 'alignleft'
    ),

    'aligncenter' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_CENTER',
        'icon'   => 'fa fa-align-center',
        'format' => 'aligncenter'
    ),

    'alignright' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_RIGHT',
        'icon'   => 'fa fa-align-right',
        'format' => 'alignright'
    ),

    'justify' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_JUSTIFY',
        'icon'   => 'fa fa-align-justify',
        'format' => 'justify'
    ),

    'orderedlist' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_LIST_ORDERED',
        'icon'   => 'fa fa-list-ol',
        'format' => 'orderedlist'
    ),

    'unorderedlist' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_LIST_UNORDERED',
        'icon'   => 'fa fa-list-ul',
        'format' => 'unorderedlist'
    ),

    'indent' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_LIST_INDENT',
        'icon'   => 'fa fa-indent',
        'format' => 'indent'
    ),

    'outdent' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_LIST_OUTDENT',
        'icon'   => 'fa fa-outdent',
        'format' => 'outdent'
    ),

    'clear' => array(
        'title'  => 'COM_EASYBLOG_COMPOSER_CLEAR_FORMATTING',
        'icon'   => 'fa fa-ban',
        'format' => 'clear'
    )
);
?>
<div class="eb-composer-field eb-font-formatting <?php echo $classname; ?>" data-type="font-formatting">
    <div class="eb-pills">
    <?php foreach ($layout as $itemgroup) { ?>
        <div class="eb-pill-group <?php echo $itemgroup['class']; ?>">
            <div class="eb-pill">
            <?php
                foreach ($itemgroup['actions'] as $actionId) {
                    $action = $actions[$actionId];
            ?>
                <div class="eb-pill-item" data-eb-font-format-option data-format="<?php echo $action['format']; ?>">
                    <i class="<?php echo $action['icon']; ?>"></i><span><?php echo JText::_($action['title']); ?></span>
                </div>
            <?php } ?>
            </div>
        </div>
    <?php } ?>
    </div>
</div>