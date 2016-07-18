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
?>
<span class="input-append">
    <input type="text" id="<?php echo $id;?>_name" readonly="readonly" value="<?php echo $title; ?>" disabled="disabled" class="input-large disabled" data-category-title />
    <a rel="{handler: 'iframe', size: {x: 900, y: 500}}" href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=categories&tmpl=component&browse=1&browsefunction=insertCategory' );?>" class="modal btn btn-primary">
        <i class="icon-file"></i> <?php echo JText::_('COM_EASYBLOG_MENU_SELECT_CATEGORY_TITLE'); ?>
    </a>
    <a href="javascript:void(0);" class="btn" data-remove-category>
        <i class="icon-remove"></i>
    </a>
</span>

<input type="hidden" id="<?php echo $id;?>_id" name="<?php echo $name;?>" value="<?php echo $value;?>" data-category-id />

<script type="text/javascript">
EasyBlog.ready(function($){
    var categoryTitle = $('[data-category-title]');
    var categoryId = $('[data-category-id]');
    var removeCategory = $('[data-remove-category]');

    removeCategory.on('click', function() {

        categoryTitle.val('<?php echo JText::_('COM_EASYBLOG_MENU_SELECT_CATEGORY_TITLE', true);?>');
        categoryId.val('');
    });

    window.insertCategory = function(id, title) {
        categoryId.val(id);
        categoryTitle.val(title);
        SqueezeBox.close();
    }
});
</script>