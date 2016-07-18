<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

$gridPath=1;
?> 
<?php if ($config->get('main_ratings')) { ?>
<script type="text/javascript">
EasyBlog.require()
.script('ratings')
.done(function($) {

    $('#fd.mod_easyblograndompost [data-rating-form]').implement(EasyBlog.Controller.Ratings);
});

</script>
<?php } ?>
<div id="fd" class="eb eb-mod mod_easyblograndompost<?php echo $params->get('moduleclass_sfx'); ?> eb-responsive">

    <?php if ($posts) { ?>
    <div class="eb-mod<?php if ($layout == 'horizontal') { echo " mod-items-grid clearfix"; } ?>">
        <?php foreach ($posts as $post) { ?>
            <?php require(JModuleHelper::getLayoutPath('mod_easyblograndompost', 'default_item')); ?>
        <?php } ?>
    </div>
    <?php } ?>
</div>
