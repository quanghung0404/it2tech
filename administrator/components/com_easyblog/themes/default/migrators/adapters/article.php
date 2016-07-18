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
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_NOTICE'); ?></div>
			</div>

			<div class="panel-body">
	            <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
							echo $lists['catid'];
						?>
		            </div>
		        </div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
							echo $lists['authorid'];
						?>
		            </div>
		        </div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_STATE'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_STATE'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_STATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
							echo $lists['state'];
						?>
		            </div>
		        </div>

		        <?php if( $jomcommentInstalled ){ ?>
				<?php echo $this->html('settings.toggle', 'migrate_jomcomment', 'COM_EASYBLOG_MIGRATOR_CONTENT_JOMCOMMENT'); ?>
				<?php } ?>

				<div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_EASYBLOG_CATEGORIES'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_EASYBLOG_CATEGORIES'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_EASYBLOG_CATEGORIES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<?php
							echo $ebCategories;
						?>
		            </div>
		        </div>

				<div style="padding-top:20px;">
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-migrate-joomla><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></a>
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
<input type="hidden" data-myblog value="<?php echo $myBlogSection; ?>" />
<input type="hidden" name="layout" value="joomla" />
