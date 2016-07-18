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
defined('_JEXEC') or die('Unauthorized Access');
?>
<form class="eb-composer-page" data-eb-composer-form="page" data-eb-composer-page>
    <div data-eb-composer-page-viewport>
        <div class="eb-composer-page-header" data-eb-composer-page-header>
        
            <div class="mobile-show">
                <div class="eb-composer-field eb-composer-field-location <?php echo !empty($post->address) ? "has-location" : ""; ?>" style="float: none;">
                    <div class="eb-composer-page-meta-text">
                        <i class="fa fa-map-marker"></i>
                        <span data-eb-composer-location-label><?php echo $post->address; ?></span>
                    </div>
                    <hr />
                </div>
            </div>

            <div class="eb-composer-page-meta row-table">
                <div class="col-cell">
                    <div class="eb-composer-field eb-composer-field-primary-category" data-category-primary>
                        <div class="dropdown_">
                            <div class="dropdown-toggle_ eb-composer-page-meta-text" data-bp-toggle="dropdown">
                                <span class="eb-composer-primary-category-title" data-category-primary-title><?php echo $primaryCategory->getTitle();?></span>
                                <i class="caret"></i>
                                <input type="hidden" name="category_id" value="<?php echo $primaryCategory->id;?>" data-category-primary-input />
                            </div>
                            <ul class="dropdown-menu" role="menu" data-category-primary-items></ul>
                        </div>
                    </div>

                    <div class="hide" data-category-primary-template>
                        <li data-category-primary-item data-title="" data-id="">
                            <a href="javascript:void(0);" data-title-text></a>
                        </li>
                    </div>
                </div>

                <div class="mobile-hide col-cell">
                    <div class="eb-composer-field eb-composer-field-location <?php echo !empty($post->address) ? "has-location" : ""; ?>">
                        <div class="eb-composer-page-meta-text">
                            <i class="fa fa-map-marker"></i>
                            <span data-eb-composer-location-label><?php echo $post->address; ?></span>
                        </div>
                        <input type="hidden" name="address" value="<?php echo $post->address;?>" />
                        <input type="hidden" name="latitude" value="<?php echo $post->latitude;?>" />
                        <input type="hidden" name="longitude" value="<?php echo $post->longitude;?>" />
                    </div>
                </div>

            </div>

            <div class="eb-composer-field eb-composer-field-title">
                <textarea name="title" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE'); ?>" data-post-title><?php echo $this->html('string.escape', $post->title); ?></textarea>
            </div>

            <div class="eb-composer-field eb-composer-field-permalink">
                <div class="permalink-editor" data-permalink-preview>
                    <span id="permalink-url" data-permalink-data><?php echo $post->permalink;?></span>

                    <?php if ($this->config->get('layout_composer_permalink')) { ?>
                    <a href="javascript:void(0);" class="btn btn-default btn-xs" style="display: inline;" data-permalink-edit><?php echo JText::_('COM_EASYBLOG_EDIT_POST_PERMALINK'); ?></a>
                    <?php } ?>
                </div>

                <div class="eb-composer-permalink-edit hide" data-permalink-editor>
                    <div class="input-group">
                        <input type="text" class="form-control" type="text" name="permalink" value="<?php echo $this->html('string.escape', $post->permalink);?>" data-permalink-input />
                        <span class="input-group-btn">
                            <a href="javascript:void(0);" class="btn btn-default" title="<?php echo JText::_('COM_EASYBLOG_SAVE'); ?>" data-permalink-save>
                                <?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON');?>
                            </a>
                            <a href="javascript:void(0);" class="btn btn-default btn-danger" data-permalink-edit-cancel>
                                <i class="fa fa-close"></i>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="eb-composer-page-body" data-eb-composer-page-body>
            <?php if ($post->isLegacy()) { ?>
                <?php  echo $this->output('site/composer/editor/legacy'); ?>
            <?php } else { ?>
                <?php echo $this->output('site/composer/editor/ebd'); ?>
            <?php } ?>
        </div>
    </div>
</form>
