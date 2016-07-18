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
<div class="eb-composer-panel" data-eb-composer-panel data-id="seo">

    <div class="eb-composer-panel-content" data-scrolly="y">
        <div data-scrolly-viewport>
            <form name="seo" class="eb-composer-seo" data-eb-composer-form>
                <div class="eb-composer-fieldset">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOM_PAGE_TITLE'); ?></strong>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <input class="form-control" type="text" id="custom_title" name="custom_title" value="<?php echo $this->html('string.escape', $post->custom_title);?>" 
                            placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOM_PAGE_TITLE_PLACEHOLDER', true);?>" />
                    </div>
                </div>

                <div class="eb-composer-fieldset">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_META_DESCRIPTION'); ?></strong>
                        <small style="text-transform: lowercase;"><span data-meta-counter>0</span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_CHARACTERS');?></small>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <div class="eb-composer-textarea">
                            <textarea class="form-control" name="description" rows="3"
                                data-meta-description
                                placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_META_DESCRIPTION_PLACEHOLDER');?>"><?php echo $post->description; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="eb-composer-fieldset">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_KEYWORDS'); ?></strong>
                        <small style="text-transform: lowercase;"><span data-keyword-counter>0</span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_WORDS');?></small>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <div class="eb-composer-textarea">
                            <div class="eb-composer-textboxlist textboxlist" data-eb-composer-seo-keywords-textboxlist style="border: none;">
                                <input type="text" class="textboxlist-textField" data-textboxlist-textField
                                    placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_KEYWORDS_PLACEHOLDER');?>"
                                    autocomplete="off" />
                            </div>

                            <?php if ($this->config->get('main_autofill_keywords')) { ?>
                            <div class="eb-composer-textarea-footer eb-composer-seo-keywords-actions">
                                <button data-eb-composer-seo-keywords-autofill-button="" class="btn btn-xs btn-primary pull-right" type="button"><i class="fa fa-bolt"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOFILL');?></button>
                                <b class="eb-loader-o pull-right" style="margin: 0 5px;"></b>
                            </div>
                            <?php } ?>

                            <textarea style="display:none;" data-eb-composer-keywords-jsondata><?php echo json_encode($post->getKeywords()); ?></textarea>
                        </div>
                        <div class="hide" data-keyword-template>
                            <div class="textboxlist-item[%== (this.locked) ? ' is-locked' : '' %]" data-textboxlist-item>
                                <span class="textboxlist-itemContent" data-textboxlist-itemContent>[%== html %]</span>
                                [% if (!this.locked) { %]
                                <div class="textboxlist-itemRemoveButton" data-textboxlist-itemRemoveButton>
                                    <i class="fa fa-close"></i>
                                </div>
                                [% } else { %]
                                    <i class="fa fa-lock"></i>
                                [% } %]
                            </div>
                        </div>
                    </div>
                </div>

                <div class="eb-composer-fieldset">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_ROBOTS'); ?></strong>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <input class="form-control" type="text" id="robots" name="robots" value="<?php echo $this->html('string.escape', $post->robots);?>" 
                            placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ROBOTS_PLACEHOLDER', true);?>" />
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
