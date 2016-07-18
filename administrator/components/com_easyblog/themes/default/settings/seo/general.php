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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_GENERAL');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEO_GENERAL_INFO');?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_anchor_nofollow', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW_DESC'); ?>

				<!-- div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW_EXCLUDE_BLOGGER'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW_EXCLUDE_BLOGGER'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW_EXCLUDE_BLOGGER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>


					<div class="col-md-7">
						<input type="text" class="form-control" name="main_anchor_nofollow_exclude" value="<?php echo $config->get( 'main_anchor_nofollow_exclude' );?>" size="10" />
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ADD_NO_FOLLOW_EXCLUDE_BLOGGER_INFO' );?>
					</div>
				</div -->

				<?php echo $this->html('settings.toggle', 'main_canonical_entry', 'COM_EASYBLOG_SETTINGS_ENABLE_CANONICAL_URL_ENTRY', 'COM_EASYBLOG_SETTINGS_ENABLE_CANONICAL_URL_ENTRY_DESC'); ?>
			</div>
		</div>

		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_INFO');?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_meta_autofillkeywords', 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_KEYWORDS', 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_KEYWORDS_DESC'); ?>

				<?php echo $this->html('settings.toggle', 'main_meta_autofilldescription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION', 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION_DESC'); ?>

				<?php echo $this->html('settings.smalltext', 'main_meta_autofilldescription_length', 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION_CHARACTER_LIMIT', 'COM_EASYBLOG_SETTINGS_WORKFLOW_METADATA_AUTO_FILL_DESCRIPTION_CHARACTER_LIMIT_DESC', 'COM_EASYBLOG_CHARACTERS'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_PERMALINKS_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_PERMALINKS_INFO');?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_sef_unicode', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_UNICODE_ALIAS'); ?>

				<?php echo $this->html('settings.toggle', 'main_url_translation', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_ENABLE_URL_TRANSLATION'); ?>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7 form-horizontal mt-20">
						<div class="form-control-static"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_NOTICE');?></div>

						<div class="list-group">
							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="default" id="defaultEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'default' ? ' checked="checked"' : '';?> class="hide" />
									<label for="defaultEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_TITLE_TYPE');?></b>
										<p class="list-group-item-text">
											http://yoursite.com/menu/view/title
										</p>
									</label>
								</div>
							</div>

							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="date" id="dateEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'date' ? ' checked="checked"' : '';?> class="hide" />
									<label for="dateEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_DATE_TYPE');?></b>
										<p class="list-group-item-text">
											http://yoursite.com/menu/view/year/month/date/title
										</p>
									</label>
								</div>
							</div>

							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="category" id="categoryEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'category' ? ' checked="checked"' : '';?> class="hide" />
									<label for="categoryEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_CATEGORY_TYPE');?></b>
										<p class="list-group-item-text">
											http://yoursite.com/menu/view/category/title
										</p>
									</label>
								</div>
							</div>

							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="datecategory" id="dateCategoryEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'datecategory' ? ' checked="checked"' : '';?> class="hide" />
									<label for="dateCategoryEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_CATEGORY_DATE_TYPE');?></b>
										<p class="list-group-item-text">
											http://yoursite.com/menu/view/category/year/month/date/title
										</p>
									</label>
								</div>
							</div>

							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="simple" id="simpleEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'simple' ? ' checked="checked"' : '';?> class="hide" />
									<label for="simpleEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_SIMPLE_TYPE');?></b>
										<p class="list-group-item-text">
											http://yoursite.com/menu/title
										</p>
									</label>
								</div>
							</div>

							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="simplecategory" id="simpleCategoryEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'simplecategory' ? ' checked="checked"' : '';?> class="hide" />
									<label for="simpleCategoryEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_FORMAT_SIMPLE_CATEGORY_TYPE');?></b>
										<p class="list-group-item-text">
											http://yoursite.com/menu/category/title
										</p>
									</label>
								</div>
							</div>

							<div class="list-group-item">
								<div class="eb-radio">
									<input type="radio" value="custom" id="customEntry" name="main_sef" <?php echo $this->config->get('main_sef') == 'custom' ? ' checked="checked"' : '';?> class="hide" />
									<label for="customEntry">
										<b class="form-control-static list-group-item-heading"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_SEF_CUSTOM_TYPE');?></b>
										<p class="list-group-item-text">
											<span style="line-height:20px;">http://yoursite.com/menu/view/</span>
											<input type="text" class="form-control" name="main_sef_custom" value="<?php echo $this->config->get( 'main_sef_custom' );?>" style="width: 200px !important; display: inline-block" />
											<span style="line-height:20px;">/title</span>
										</p>
									</label>
								</div>
							</div>
						</div>


						<div class="alert alert-warning mt-20">
							<?php echo JText::_( 'COM_EASYBLOG_AVAILABLE_VALUES_FOR_SEF' );?>:<br /><br />

							%month% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_MONTH_NAME' );?></span><br />
							%day% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_DAY_NAME' );?></span><br />
							%year_num% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_YEAR_NUMBER' );?></span><br />
							%month_num% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_MONTH_NUMBER' );?></span><br />
							%day_num% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_DAY_NUMBER' );?></span><br />
							%category% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_CATEGORY_NAME' );?></span><br />
							%category_id% - <span><?php echo JText::_( 'COM_EASYBLOG_CUSTOM_SEF_CATEGORY_ID' );?></span><br /><br />

							<?php echo JText::_( 'COM_EASYBLOG_EXAMPLE' );?>: %year_num%/%title%
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
