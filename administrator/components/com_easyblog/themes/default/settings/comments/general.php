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
    			<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'main_comment', $this->config->get('main_comment')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_('select.option', 'desc', JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_OPTIONS_DESCENDING'));
                            $listLength[] = JHTML::_('select.option', 'asc', JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_OPTIONS_ASCENDING'));
                            echo JHTML::_('select.genericlist', $listLength, 'comment_sort', 'class="form-control input-box"', 'value', 'text', $this->config->get('comment_sort' , 'desc'));
                        ?>
                    </div>
                </div>                

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_bbcode', $this->config->get('comment_bbcode')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_likes', $this->config->get('comment_likes')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'main_allowguestviewcomment', $this->config->get('main_allowguestviewcomment')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_REGISTRATION_WHEN_COMMENTING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_REGISTRATION_WHEN_COMMENTING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_REGISTRATION_WHEN_COMMENTING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_registeroncomment', $this->config->get('comment_registeroncomment')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_autotitle', $this->config->get('comment_autotitle')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-lg-3">
                    	       <input type="text" class="form-control text-center" name="comment_maxthreadedlevel" value="<?php echo $config->get( 'comment_maxthreadedlevel' );?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_COMMENTS_ENABLE_AUTO_HYPERLINKS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_COMMENTS_ENABLE_AUTO_HYPERLINKS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_COMMENTS_ENABLE_AUTO_HYPERLINKS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_autohyperlink', $this->config->get('comment_autohyperlink')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_autosubscribe', $this->config->get('comment_autosubscribe')); ?>
                    </div>
                </div> 
            </div> 
        </div>

	</div>

	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_DESC');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_show_title', $this->config->get('comment_show_title')); ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_requiretitle', $this->config->get('comment_requiretitle')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_show_email', $this->config->get('comment_show_email')); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_require_email', $this->config->get('comment_require_email')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_show_website', $this->config->get('comment_show_website')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_require_website', $this->config->get('comment_require_website')); ?>
                    </div>
                </div>
            </div>
        </div>

		<div class="panel">
            <div class="panel-head">
    			<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_TNC_TITLE'); ?></b>
    			<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_DESC');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_tnc', $this->config->get('comment_tnc')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<textarea name="comment_tnctext" class="form-control" rows="15"><?php echo str_replace('<br />', "\n", $config->get('comment_tnctext' )); ?></textarea>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
