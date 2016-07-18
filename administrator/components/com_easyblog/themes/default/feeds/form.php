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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<div class="row">
	    <div class="col-lg-6">
	        <div class="panel">
	        	<div class="panel-head">
	            	<b><?php echo JText::_('COM_EASYBLOG_FEEDS_DETAILS');?></b>
	            	<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_FEEDS_DETAILS_INFO');?></div>
	            </div>

	            <div class="panel-body">
		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_TITLE'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_TITLE'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<input class="form-control" id="title" name="title" size="55" maxlength="255" value="<?php echo $feed->title; ?>"/>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_URL'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_URL'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<input class="form-control" id="url" name="url" size="55" value="<?php echo $feed->get( 'url' );?>" />
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISHED'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISHED'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISHED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'published', $feed->published); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_CRON'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_CRON'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_CRON_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'cron', $feed->cron); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_CRON_INTERVAL'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_CRON_INTERVAL'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_CRON_INTERVAL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<div class="row">
		                		<div class="col-sm-3">
		                			<input class="form-control" id="interval" name="interval" size="3" style="text-align: center;" value="<?php echo $feed->get( 'interval' );?>" /> <?php echo JText::_( 'COM_EASYBLOG_MINUTES' );?>
		                		</div>
		                	</div>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_SHOW_AUTHOR'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_SHOW_AUTHOR'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_SHOW_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'author', $feed->author); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_COPYRIGHT_TEXT'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_COPYRIGHT_TEXT'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_COPYRIGHT_TEXT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<input type="text" class="form-control" name="copyrights" size="55" value="<?php echo $params->get( 'copyrights' );?>" />
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_INCLUDE_ORIGINAL_LINK'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_INCLUDE_ORIGINAL_LINK'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_SHOW_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'sourceLinks', $params->get('sourceLinks')); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_AMOUNT'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_AMOUNT'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_AMOUNT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<div class="row">
		                		<div class="col-sm-3">
		                			<input type="text" class="form-control" name="feedamount" size="3" value="<?php echo $params->get( 'feedamount' );?>" />
		                		</div>
		                	</div>
		                </div>
		            </div>
	            </div>
	        </div>
	    </div>

	    <div class="col-lg-6">
	        <div class="panel">
	        	<div class="panel-head">
	            	<b><?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS');?></b>
	            	<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS_INFO');?></div>
	            </div>

	            <div class="panel-body">
		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_ITEM'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_ITEM'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_ITEM_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<select name="item_published" class="form-control">
								<option value="1" <?php echo ($feed->item_published == '1') ? 'selected' : '' ; ?> ><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></option>
								<option value="0" <?php echo ($feed->item_published == '0') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_UNPUBLISHED' ); ?></option>
								<option value="4" <?php echo ($feed->item_published == '4') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_PENDING' ); ?></option>
							</select>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_LANGUAGE'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_LANGUAGE'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_LANGUAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<select name="language" class="form-control">
								<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
								<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' , $feed->language );?>
							</select>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'item_frontpage', $feed->item_frontpage); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'autopost' ,$params->get('autopost')); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_NOTIFY_USERS'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_NOTIFY_USERS'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISH_NOTIFY_USERS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<?php echo $this->html('grid.boolean', 'notify', $params->get('notify', true)); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_CATEGORY'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_CATEGORY'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_CATEGORY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<span id="category_name"><?php echo ( !empty($category) ) ? $category : ''; ?></span>
							<a href="index.php?option=com_easyblog&view=categories&tmpl=component&browse=1" rel="{handler: 'iframe', size: {x: 750, y: 475}}" class="modal btn btn-default">
								<i class="fa fa-folder"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SELECT_CATEGORY_BUTTON');?>
							</a>
							<input type="hidden" name="item_category" value="<?php echo $feed->get( 'item_category' );?>" id="item_category" />
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_AUTHOR'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_AUTHOR'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<span id="author_name"><?php echo ( !empty($author) ) ? $author : ''; ?></span>
							<a href="index.php?option=com_easyblog&view=bloggers&tmpl=component&browse=1" rel="{handler: 'iframe', size: {x: 750, y: 475}}" class="modal btn btn-default">
								<i class="fa fa-user"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SELECT_AUTHOR');?>
							</a>
							<input type="hidden" name="item_creator" value="<?php echo $feed->get( 'item_creator' );?>" id="item_creator" />
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_TEAM'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_TEAM'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_TEAM_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<span id="team_name"><?php echo ( !empty($teamName) ) ? $teamName : ''; ?></span>
							<a href="index.php?option=com_easyblog&view=teamblogs&tmpl=component&browse=1" rel="{handler: 'iframe', size: {x: 750, y: 475}}" class="modal btn btn-default">
								<i class="fa fa-user"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SELECT_TEAM');?>
							</a>
							<input type="hidden" name="item_team" value="<?php echo $feed->get( 'item_team' );?>" id="item_team" />
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_GET_FULL_TEXT'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_GET_FULL_TEXT'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_GET_FULL_TEXT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<?php echo $this->html('grid.boolean', 'item_get_fulltext' ,$feed->item_get_fulltext); ?>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
							<select name="item_content" class="form-control">
								<option value="intro" <?php echo ($feed->item_content == 'intro') ? 'selected' : '' ; ?> ><?php echo JText::_( 'COM_EASYBLOG_FEEDS_INTROTEXT' ); ?></option>
								<option value="content" <?php echo ($feed->item_content == 'content') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_FEEDS_MAINTEXT' ); ?></option>
							</select>
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="page_title" class="col-md-4">
		                    <?php echo JText::_('COM_EASYBLOG_FEEDS_ALLOWED_TAGS'); ?>

		                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FEEDS_ALLOWED_TAGS'); ?>"
		                        data-content="<?php echo JText::_('COM_EASYBLOG_FEEDS_ALLOWED_TAGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		                </label>

		                <div class="col-md-8">
		                	<textarea name="item_allowed_tags" class="form-control"><?php echo $params->get( 'allowed' , '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>' ); ?></textarea>
		                </div>
		            </div>
	            </div>
	        </div>
	   	</div>
	</div>


	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $feed->id;?>" />
</form>
