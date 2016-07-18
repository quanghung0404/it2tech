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
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_FEEDBURNER_URL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_FEEDBURNER_URL'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_FEEDBURNER_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input class="form-control" type="text" id="feedburner_url" name="feedburner_url" value="<?php echo $this->html('string.escape', $feedburner->url);?>" />
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->config->get('integration_google_adsense_enable')) { ?>
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE'); ?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_ENABLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_ENABLE'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'adsense_published', $adsense->published); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_CODE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_CODE'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_CODE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <textarea id="adsense_code" name="adsense_code" class="form-control"><?php echo $adsense->code; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_DISPLAY_IN'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_DISPLAY_IN'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_DISPLAY_IN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <select name="adsense_display" class="form-control">
                            <option value="both"<?php echo ($adsense->display == 'both')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_BOTH_HEADER_AND_FOOTER_OPTION'); ?></option>
                            <option value="header"<?php echo ($adsense->display == 'header')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_HEADER_OPTION'); ?></option>
                            <option value="footer"<?php echo ($adsense->display == 'footer')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_FOOTER_OPTION'); ?></option>
                            <option value="beforecomments"<?php echo ($adsense->display == ' beforecomments')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_BEFORE_COMMENTS_OPTION'); ?></option>
                            <option value="userspecified"<?php echo ($adsense->display == 'userspecified')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_USER_SPECIFIED'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_GOOGLE'); ?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_GOOGLE_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input type="text" class="form-control" name="google_profile_url" id="google_profile_url" value="<?php echo $this->html('string.escape', $bloggerParams->get('google_profile_url'));?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL_SHOW'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL_SHOW'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_GOOGLE_PROFILE_URL_SHOW_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'show_google_profile_url', $bloggerParams->get('show_google_profile_url')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>