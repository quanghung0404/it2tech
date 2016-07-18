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
	            <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_INTEGRATIONS_TITLE'); ?></b>
	            <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_INTEGRATIONS_DESC'); ?></div>
            </div>

            <div class="panel-body">
	            <div class="form-group">
	                <label for="page_title" class="col-md-5">
	                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AKISMET'); ?>

	                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AKISMET'); ?>" 
	                        data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AKISMET_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
	                </label>

	                <div class="col-md-7">
	                    <?php echo $this->html('grid.boolean', 'comment_akismet', $this->config->get('comment_akismet')); ?>
	                </div>
	            </div>

	            <div class="form-group">
	                <label for="page_title" class="col-md-5">
	                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_API_KEY'); ?>

	                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_API_KEY'); ?>" 
	                        data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
	                </label>

	                <div class="col-md-7">
	                	<input type="text" class="form-control" name="comment_akismet_key" value="<?php echo $this->config->get('comment_akismet_key');?>" size="60" />
	                </div>
	            </div>
            </div>
        </div>

        <div class="panel">
       		<div class="panel-head">
	            <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_CAPTCHA'); ?></b>
	            <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_CAPTCHA_DESC'); ?></div>
            </div>

            <div class="panel-body">
	            <div class="form-group">
	                <label for="page_title" class="col-md-5">
	                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA'); ?>

	                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA'); ?>" 
	                        data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
	                </label>

	                <div class="col-md-7">
	                    <?php echo $this->html('grid.boolean', 'comment_captcha', $this->config->get('comment_captcha')); ?>
	                </div>
	            </div>

	            <div class="form-group">
	                <label for="page_title" class="col-md-5">
	                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_REGISTERED'); ?>

	                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_REGISTERED'); ?>" 
	                        data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_REGISTERED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
	                </label>

	                <div class="col-md-7">
	                	<?php echo $this->html('grid.boolean', 'comment_captcha_registered', $this->config->get('comment_captcha_registered')); ?>
	                </div>
	            </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
			    <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_INTEGRATIONS_TITLE'); ?></b>
			    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_INTEGRATIONS_DESC'); ?></div>
		    </div>

		    <div class="panel-body">
			    <div class="form-group">
			        <label for="page_title" class="col-md-5">
			            <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA'); ?>

			            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA'); ?>" 
			                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_RECAPTCHA_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
			        </label>

			        <div class="col-md-7">
			            <?php echo $this->html('grid.boolean', 'comment_recaptcha', $this->config->get('comment_recaptcha')); ?>
			        </div>
			    </div>

			    <div class="form-group">
			        <label for="page_title" class="col-md-5">
			            <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY'); ?>

			            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY'); ?>" 
			                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
			        </label>

			        <div class="col-md-7">
			        	<input type="text" class="form-control" name="comment_recaptcha_public" value="<?php echo $this->config->get('comment_recaptcha_public');?>" size="60" />
			        </div>
			    </div>

			    <div class="form-group">
			        <label for="page_title" class="col-md-5">
			            <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PRIVATE_KEY'); ?>

			            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PRIVATE_KEY'); ?>" 
			                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PRIVATE_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
			        </label>

			        <div class="col-md-7">
			        	<input type="text" class="form-control" name="comment_recaptcha_private" value="<?php echo $this->config->get('comment_recaptcha_private');?>" size="60" />
			        </div>
			    </div>

			    <div class="form-group">
			        <label for="page_title" class="col-md-5">
			            <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME'); ?>

			            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME'); ?>" 
			                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
			        </label>

			        <div class="col-md-7">
						<select name="comment_recaptcha_theme" class="form-control">
							<option value="light"<?php echo $this->config->get('comment_recaptcha_theme') == 'light' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_LIGHT');?></option>
							<option value="dark"<?php echo $this->config->get('comment_recaptcha_theme') == 'dark' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_DARK');?></option>
						</select>
			        </div>
			    </div>

			    <div class="form-group">
			        <label for="page_title" class="col-md-5">
			            <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE'); ?>

			            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE'); ?>" 
			                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
			        </label>

			        <div class="col-md-7">
						<select name="comment_recaptcha_lang" class="form-control">
							<option value="en"<?php echo $this->config->get('comment_recaptcha_lang') == 'en' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_ENGLISH');?></option>
							<option value="ru"<?php echo $this->config->get('comment_recaptcha_lang') == 'ru' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_RUSSIAN');?></option>
							<option value="fr"<?php echo $this->config->get('comment_recaptcha_lang') == 'fr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_FRENCH');?></option>
							<option value="de"<?php echo $this->config->get('comment_recaptcha_lang') == 'de' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_GERMAN');?></option>
							<option value="nl"<?php echo $this->config->get('comment_recaptcha_lang') == 'nl' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_DUTCH');?></option>
							<option value="pt"<?php echo $this->config->get('comment_recaptcha_lang') == 'pt' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_PORTUGUESE');?></option>
							<option value="tr"<?php echo $this->config->get('comment_recaptcha_lang') == 'tr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_TURKISH');?></option>
							<option value="es"<?php echo $this->config->get('comment_recaptcha_lang') == 'es' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE_SPANISH');?></option>
						</select>
			        </div>
			    </div>
		    </div>
		</div>
    </div>
</div>