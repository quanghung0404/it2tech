<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">

	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_APP'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_ENABLE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'main_autopost_linkedin', $this->config->get('main_autopost_linkedin')); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_CLIENT_ID'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_linkedin_id', $this->config->get('main_autopost_linkedin_id')); ?>
								<div class="small">
									<a href="http://stackideas.com/docs/easydiscuss/administrators/autoposting/linkedin-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_CLIENT_SECRET'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_linkedin_secret', $this->config->get('main_autopost_linkedin_secret')); ?>

								<div class="small">
									<a href="http://stackideas.com/docs/easydiscuss/administrators/autoposting/linkedin-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>
						<div class="form-group">

							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_SIGN_IN'); ?>
							</div>

							<div class="col-md-7">
								<?php if ($associated) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=linkedin');?>" class="btn btn-danger">
									<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
								</a>
								<?php } else { ?>
								<a href="javascript:void(0)" data-linkedin-login>
									<img src="<?php echo JURI::root();?>media/com_easydiscuss/images/linkedin_signon.png" />
								</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_COMPANIES'); ?>

				<div class="panel-body">
					<?php if ($associated) { ?>
					<div class="form-horizontal">
						<div class="form-group">						
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN_COMPANIES'); ?>
							</div>

							<div class="col-md-7 control-label">
	                            <?php if ($companies) { ?>
	                            <select name="main_autopost_linkedin_company_id[]" class="form-control" multiple="multiple" size="10">
	                                <?php foreach ($companies as $company) { ?>
	                                <option value="<?php echo $company->id;?>" <?php echo in_array($company->id, $storedCompanies) ? ' selected="selected"' : '';?>>
	                                    <?php echo $company->title;?>
	                                </option>
	                                <?php } ?>
	                            </select>

	                            <p class="mt-5 small"><?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SELECT_MULTIPLE'); ?></p>
	                            <?php } else { ?>
	                                <p><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_AUTOPOST_NO_COMPANIES_YET'); ?></p>
	                            <?php } ?>
							</div>
						</div>
					</div>
					<?php } else { ?>
						<p class="small"><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_AUTOPOST_SIGNIN_FIRST');?></p>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_POST_MESSAGE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textarea', 'main_autopost_linkedin_message', $this->config->get('main_autopost_linkedin_message'));?>

								<p class="small mt-10">
									<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_AUTOPOST_POST_MESSAGE_FOOTNOTE'); ?>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="type" value="linkedin" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
</form>
