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
            	<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_INFO');?></div>
            </div>

           	<div class="panel-body">
				<div class="has-tip">
					<textarea name="social_addthis_customcode" class="inputbox full-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('social_addthis_customcode');?></textarea>
				</div>
			</div>
        </div>

    </div>

    <div class="col-lg-6">
        <div class="panel">
        	<div class="panel-head">
            	<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE');?></b>
            	<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE_DESC'); ?></div>
            </div>

            <div class="panel-body">
		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_STYLE');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
		            	<div class="row">
		            		<div class="col-lg-4">
		            			<div class="mb-10">
									<input type="radio" name="social_addthis_style" id="addthis_normal" value="1"<?php echo $this->config->get('social_addthis_style') == '1' ? ' checked="checked"' : '';?> />
									&nbsp; <label for="addthis_normal"><?php echo JText::_('COM_EASYBLOG_SETTINGS_ADDTHIS_BUTTON_EXTENDED');?></label>
		            			</div>
		            			<img src="<?php echo $this->getPathURI('/images/addthis_button1.png');?>" />
		            		</div>

		            		<div class="col-lg-4">
		            			<div class="mb-10">
									<input type="radio" name="social_addthis_style" id="addthis_compact" value="2"<?php echo $this->config->get('social_addthis_style') == '2' ? ' checked="checked"' : '';?> />
									&nbsp; <label for="addthis_compact"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTON_PLAIN');?></label>
		            			</div>
		            			<img src="<?php echo $this->getPathURI('/images/addthis_button2.png');?>" />
		            		</div>
						</div>
		            </div>
		        </div>
	        </div>
        </div>
    </div>
</div>
