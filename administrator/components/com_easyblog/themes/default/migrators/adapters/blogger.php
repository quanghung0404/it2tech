<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_DESC'); ?></div>
			</div>

			<div class="panel-body">
				<p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_NOTICE'); ?></p>

	            <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_FILE'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_FILE'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_FILE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
							echo $lists;
						?>
		            </div>
		        </div>

	            <div class="form-group">
	                <label for="page_title" class="col-md-5">
	                    <?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_AS'); ?>

	                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_AS'); ?>"
	                        data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_AS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
	                </label>

	                <div class="col-md-7">
	                	<input type="text" name="authorid" class="form-control" value="<?php echo EasyBlogHelper::getDefaultSAIds(); ?>" data-author-id />
	                </div>
	            </div>

	            <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_INTO_CATEGORY'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_INTO_CATEGORY'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_INTO_CATEGORY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
							echo $categories;
						?>
		            </div>
		        </div>

				<div style="padding-top:20px;">
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-migrate-blogger><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></a>
				</div>
			</div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
        	<div class="panel-head">
        		<b><?php echo JText::_('COM_EASYBLOG_MIGRATOR_PROGRESS');?></b>
                <span data-progress-loading class="eb-loader-o size-sm hide"></span>
        	</div>

        	<div class="panel-body">
        		<div data-progress-empty><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
        		<div data-progress-status style="overflow:auto; height:98%;max-height: 300px;"></div>
			</div>
		</div>

		<div class="panel">
        	<div class="panel-head">
        		<b><?php echo JText::_('COM_EASYBLOG_MIGRATOR_STATISTIC');?></b>
        	</div>

        	<div class="panel-body">
        		<div data-progress-stat style="overflow:auto; height:98%;"></div>
			</div>
		</div>
    </div>
</div>
<input type="hidden" name="layout" value="blogger" />
