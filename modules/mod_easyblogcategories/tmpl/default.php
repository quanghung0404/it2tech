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
?>
<div id="fd" class="eb eb-mod mod_easyblogcategories <?php echo $params->get('moduleclass_sfx') ?>">
    <?php if ($layoutType == 'toggle') { ?>
    <div class="mod-items-compact cell-top">
       <?php foreach ($results as $category) { ?>
           <div class="mod-item">
               <div class="mod-table">
                   <div class="mod-cell cell-tight">
                       <i class="fa fa-folder mr-10"></i>
                   </div>
                   <div class="mod-cell">
                       <div class="mod-table">
                           <div class="mod-cell">
                               <a href="<?php echo EB::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id); ?>">
                                   <?php echo $category->title; ?>
                               </a>
                              <?php if ($params->get('showcount', true)) { ?>
                                <span class="mod-muted">(<?php echo $category->cnt; ?>)</span>
                              <?php } ?>
                           </div>
                           <?php if ($category->childs) { ?>
                           <a class="mod-cell cell-tight mod-muted" data-bp-toggle="collapse" href="#cat-<?php echo $category->id; ?>">
                               <i class="fa fa-chevron-down"></i>
                           </a>
                           <?php } ?>
                       </div>

                      <?php if ($category->childs) { ?>
                        <?php echo modEasyBlogCategoriesHelper::accessNestedToggleCategories($category); ?>
                      <?php } ?>
                   </div>
               </div>
           </div>
       <?php } ?>
    </div>
    <?php } ?>
</div>
