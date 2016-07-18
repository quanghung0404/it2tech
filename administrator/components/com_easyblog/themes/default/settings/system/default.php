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
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_SETTINGS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_SETTINGS_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'ajax_use_index', $this->config->get('ajax_use_index')); ?>
                        <div class="mt-10">
                            <p class="text-muted"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL_INFO', rtrim(JURI::root(), '/'));?></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="easyblog_environment" class="form-control">
                            <option value="static"<?php echo $this->config->get( 'easyblog_environment' ) == 'static' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_STATIC' ); ?></option>
                            <option value="optimized"<?php echo $this->config->get( 'easyblog_environment' ) == 'optimized' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED' ); ?></option>
                            <option value="development"<?php echo $this->config->get( 'easyblog_environment' ) == 'development' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT' ); ?></option>
                        </select>
                        <div class="mt-10">
                            <p><b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_STATIC'); ?></b> - <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_STATIC_DESC'); ?></p>
                            <p><b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED'); ?></b> - <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED_DESC'); ?></p>
                            <div><b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT'); ?></b> - <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT_DESC'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="easyblog_mode" class="form-control">
                            <option value="compressed"<?php echo $this->config->get( 'easyblog_mode' ) == 'compressed' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_COMPRESSED' ); ?></option>
                            <option value="uncompressed"<?php echo $this->config->get( 'easyblog_mode' ) == 'uncompressed' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_UNCOMPRESSED' ); ?></option>
                        </select>
                        <div class="mt-10">
                            <p><b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_COMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_COMPRESSED_DESC'); ?></p>
                            <p><b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_UNCOMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_UNCOMPRESSED_DESC'); ?></p>
                            <div class="text-muted"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_JAVASCRIPT_COMPRESSION_UNCOMPRESSED_NOTE'); ?></small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_ENABLE_CDN'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_ENABLE_CDN'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_ENABLE_CDN_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'enable_cdn', $this->config->get('enable_cdn')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_URL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_URL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_CDN_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="cdn_url" value="<?php echo $this->config->get('cdn_url');?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_API_KEY');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_API_KEY_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_ENTER_API_KEY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_ENTER_API_KEY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_ENTER_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div>
                            <input type="text" class="form-control" name="main_apikey" value="<?php echo $this->config->get('main_apikey');?>" />
                        </div>
                        <br />
                        <a href="http://stackideas.com/dashboard" target="_blank"><?php echo JText::_('COM_EASYBLOG_OBTAIN_API_KEY_LINK');?></a>
                    </div>
                </div>
            </div>
        </div>



        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-sm-3">
                                <input type="text" name="main_orphanitem_ownership" class="text-center form-control" value="<?php echo $this->config->get('main_orphanitem_ownership', $ownerIds);?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_PROFILING'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_PROFILING_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_DB_PROFILING_ENABLED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_DB_PROFILING_ENABLED'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_DB_PROFILING_ENABLED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php echo $this->html('grid.boolean', 'db_profiling', $this->config->get('db_profiling')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->html('form.action'); ?>
<input type="hidden" name="page" value="system" />
<input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>
