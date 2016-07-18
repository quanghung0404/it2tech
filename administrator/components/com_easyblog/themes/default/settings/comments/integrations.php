<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row form-horizontal">
    <div class="col-lg-6">

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_DESC'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'main_comment_multiple', $this->config->get('main_comment_multiple')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_easyblog', $this->config->get('comment_easyblog', 1)); ?>
                    </div>
                </div>
            </div>
        </div>

       <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php if ($komento) { ?>
    						<?php echo $this->html('grid.boolean', 'comment_komento', $this->config->get('comment_komento')); ?>
    					<?php } else { ?>
    						<div class="form-control-static"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO_NOT_INSTALLED'); ?></div>
    					<?php } ?>
                    </div>
                </div>
            </div>
        </div>

       <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php if ($easydiscuss) { ?>
    						<?php echo $this->html('grid.boolean', 'comment_easydiscuss', $this->config->get('comment_easydiscuss')); ?>
    					<?php } else { ?>
    						<div class="form-control-static"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS_NOT_INSTALLED'); ?></div>
    					<?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_disqus', $this->config->get('comment_disqus')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="input-group input-group-link">
        					<input type="text" name="comment_disqus_code" class="form-control" value="<?php echo $this->config->get('comment_disqus_code');?>" />
                            <span class="input-group-btn">
            					<a href="http://stackideas.com/docs/easyblog/administrators/comments/integrating-with-disqus" class="btn btn-default">
                                    <i class="fa fa-life-ring"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_hypercomments', $this->config->get('comment_hypercomments')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS_WIDGETID'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS_WIDGETID'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS_WIDGETID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="comment_hypercomments_widgetid" class="form-control" value="<?php echo $this->config->get('comment_hypercomments_widgetid');?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_easysocial', $this->config->get('comment_easysocial')); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_facebook', $this->config->get('comment_facebook')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COLOUR_SCHEME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COLOUR_SCHEME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COLOUR_SCHEME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="comment_facebook_colourscheme" class="form-control">
                            <option<?php echo $this->config->get( 'comment_facebook_colourscheme' ) == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
                            <option<?php echo $this->config->get( 'comment_facebook_colourscheme' ) == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_intensedebate', $this->config->get('comment_intensedebate')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="input-group input-group-link">
        					<input type="text" class="form-control" value="<?php echo $this->config->get( 'comment_intensedebate_code' );?>" name="comment_intensedebate_code" />
                            <span class="input-group-btn">
        					   <a href="http://stackideas.com/docs/easyblog/administrators/comments/integrating-with-intense-debate" class="btn btn-default">
                                    <i class="fa fa-life-ring"></i>
                               </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<?php echo $this->html('grid.boolean', 'comment_compojoom', $this->config->get('comment_compojoom')); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php if ($jComment) { ?>
    						<?php echo $this->html('grid.boolean', 'comment_jcomments', $this->config->get('comment_jcomments')); ?>
    					<?php } else { ?>
    						<div class="form-control-static"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT_NOT_FOUND'); ?></div>
    					<?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php if ($rsComment) { ?>
    						<?php echo $this->html('grid.boolean', 'comment_rscomments', $this->config->get('comment_rscomments')); ?>
    					<?php } else { ?>
    						<div class="form-control-static"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RSCOMMENTS_NOT_FOUND'); ?></div>
    					<?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE'); ?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php echo $this->html('grid.boolean', 'comment_livefyre', $this->config->get('comment_livefyre')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE_SITEID'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE_SITEID'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_LIVEFYRE_SITEID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="input-group input-group-link">
        					<input type="text" name="comment_livefyre_siteid" class="form-control" value="<?php echo $this->config->get('comment_livefyre_siteid');?>" />

                            <span class="input-group-btn">
        					   <a href="http://stackideas.com/docs/easyblog/administrators/comments/integrating-with-livefyre" target="_blank" class="btn btn-default">
                                    <i class="fa fa-life-ring"></i>
                               </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
