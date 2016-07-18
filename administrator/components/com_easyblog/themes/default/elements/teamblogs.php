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
    <input type="text" id="<?php echo $id;?>_name" readonly="readonly" value="<?php echo $title; ?>" disabled="disabled" class="input-large disabled" data-team-title />
    <a rel="{handler: 'iframe', size: {x: 900, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_easyblog&view=teamblogs&tmpl=component&browse=1&browsefunction=insertTeam' );?>" class="modal btn btn-primary">
        <i class="icon-users"></i> <?php echo JText::_('COM_EASYBLOG_MENU_OPTIONS_SELECT_TEAM'); ?>
    </a>
</span>

<input type="hidden" id="<?php echo $id;?>_id" name="<?php echo $name;?>" value="<?php echo $value;?>" data-team-id />

<script type="text/javascript">

EasyBlog.ready(function($){
    window.insertTeam = function(id, name) {

        $('[data-team-id]').val(id);
        $('[data-team-title]').val(name);

        SqueezeBox.close();
    }
});
</script>
