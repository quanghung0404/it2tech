<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
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
                <b><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_GENERAL');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_NAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_NAME'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input class="form-control" name="title" id="title" value="<?php echo $this->escape($team->title);?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_ALIAS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_ALIAS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_ALIAS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input class="form-control" name="alias" id="alias" value="<?php echo $this->escape($team->alias);?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_DESCRIPTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_DESCRIPTION'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_TEAM_DESCRIPTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $editor->display( 'write_description', $team->description, '100%', '150', '5', '5' , array('article', 'image', 'readmore', 'pagebreak') ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_OTHERS');?></b>
            </div>

            <div class="panel-body">
                <?php if($this->config->get('layout_teamavatar')){ ?>
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_AVATAR'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_AVATAR'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_AVATAR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php if(! empty($team->avatar)) { ?>
                        <img style="border-style:solid; float:none;" src="<?php echo $team->getAvatar(); ?>" width="60" height="60"/><br />
                        <?php } ?>
                        <input class="mt-15" id="file-upload" type="file" name="avatar" />
                    </div>
                </div>
                <?php } ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_CREATED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_CREATED'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_CREATED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <div class="input-group date" data-date-picker>
                            <input type="text" class="form-control" name="created" />
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_PUBLISHED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_PUBLISHED'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_PUBLISHED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'published', is_null($team->published) ? true : $team->published); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ACCESS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ACCESS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ACCESS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $blogAccess;?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ALLOW_JOIN'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ALLOW_JOIN'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_ALLOW_JOIN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'allow_join', $team->allow_join); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <textarea name="keywords" id="keywords" class="form-control"><?php echo $meta->keywords; ?></textarea>

                        <div>
                            <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_KEYWORDS_INSTRUCTIONS'); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_DESCRIPTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_DESCRIPTION'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_META_DESCRIPTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <textarea name="description" id="description" class="form-control"><?php echo $meta->description; ?></textarea>
                    </div>
                </div>

                <input type="hidden" name="metaid" value="<?php echo $meta->id; ?>" />
            </div>
        </div>
    </div>
</div>

