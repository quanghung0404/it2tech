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

$readonly = ($this->acl->get('create_tag')) ? '' : 'data-eb-composer-tags-readonly';
$maxTags  = $this->config->get('max_tags_allowed');

if (!is_array($post->tags)) {

    if (empty($post->tags)) {
        $tagCount = 0;
    } else {
        $post->tags = explode(',', $post->tags);
        $tagCount = count($post->tags);
    }
} else {
    $tagCount = count($post->tags);
}
?>
<div class="eb-composer-fieldset" data-name="tags">

    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_TAGS_HEADING'); ?></strong>
        <small>
            <span class="eb-composer-tags-count" data-eb-composer-tags-count><?php echo $tagCount; ?></span><?php if ($maxTags > 0) { ?>/<?php echo $maxTags; ?><?php } ?> <?php echo JText::_("COM_EASYBLOG_TAGS"); ?>
        </small>
    </div>

    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-tags" data-eb-composer-tags <?php echo $readonly; ?> data-eb-composer-tags-max="<?php echo $maxTags; ?>">
            <div class="eb-composer-textboxlist textboxlist" data-eb-composer-tags-textboxlist>
                <input type="text"
                    class="textboxlist-textField" data-textboxlist-textField
                    placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ENTER_TAG');?>"
                    autocomplete="off" />
            </div>
            <div class="eb-composer-tags-suggestions is-empty">
                <div class="eb-composer-tags-selection">
                    <s></s>
                    <small class="empty-tags"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE'); ?></small>
                    <div class="eb-composer-tags-selection-itemgroup"></div>
                </div>
                <div class="eb-composer-tags-actions">
                    <small class="pull-left eb-composer-tags-toggle" data-eb-composer-tags-toggle-button>
                        <i class="fa fa-tags"></i>
                        <span>
                            <span data-eb-composer-tags-total>0</span>
                        </span>
                    </small>

                    <?php if ($this->config->get('main_autofill_tags')) { ?>
                    <a class="eb-composer-tags-autofill-indicator pull-left"><i class="fa fa-refresh fa-spin"></i></a>

                    <button type="button" class="btn btn-xs btn-primary pull-right" data-eb-composer-tags-autofill-button>
                        <i class="fa fa-bolt"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOFILL');?>
                    </button>

                    <b class="eb-loader-o pull-right" style="margin: 0 5px;"></b>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
        <textarea style="display:none;" data-eb-composer-tags-jsondata><?php echo json_encode($tags); ?></textarea>
    </div>
</div>

<div class="hide" data-tag-template>
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