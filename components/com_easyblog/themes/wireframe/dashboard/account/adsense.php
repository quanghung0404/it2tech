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
<div class="eb-box">
	<div class="eb-box-head">
		<i class="fa fa-google"></i>&nbsp;
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_TITLE'); ?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_DESC');?></p>
		<div class="form-horizontal">
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_ENABLE'); ?></label>
				<div class="col-md-5">
					<?php echo $this->html('grid.boolean', 'adsense_published', $adsense->published); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE'); ?></label>
				<div class="col-md-9">
					<textarea id="adsense_code" name="adsense_code" class="form-control" rows="3"><?php echo $adsense->code; ?></textarea>
					<div class="eb-box-help">
						<a href="javascript:void(0);" data-bp-toggle="collapse" data-target="#demo"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE_HELP');?></a>

						<div id="demo" class="collapse">
							<div style="padding: 10px 0 20px;">
								<div style="background: #eee; padding: 15px; border-radius: 3px">
									<ol style="margin: 0; padding: 0 0 0 20px;">
										<li>Sign in to your account at <a>http://www.google.com/adsense</a>.</li>
										<li>Visit the My ads tab.</li>
										<li>From the sidebar, choose your product. If you simply want to display ads on your website, choose "Content."</li>
										<li>Click +New ad unit.</li>
										<li>Name and customize your ad unit.</li>
										<li>Click Save and get code.</li>
										<li>Copy and paste the ad code into the box provided.</li>
									</ol>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_APPEARANCE'); ?></label>
				<div class="col-md-5">
    				<select name="adsense_display" class="form-control" data-adsense-appearence>
    					<option value="both"<?php echo ($adsense->display == 'both')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_HEADER_AND_FOOTER'); ?></option>
    					<option value="header"<?php echo ($adsense->display == 'header')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_HEADER'); ?></option>
    					<option value="footer"<?php echo ($adsense->display == 'footer')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_FOOTER'); ?></option>
    					<option value="beforecomments"<?php echo ($adsense->display == 'beforecomments')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_BEFORE_COMMENTS'); ?></option>
    					<option value="userspecified"<?php echo ($adsense->display == 'userspecified')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_USER_SPECIFIED'); ?></option>
    				</select>
				</div>
				<div class="col-md-9 col-md-offset-3 hide" data-adsense-appearence-help>
					<div class="eb-box-help">
						<?php echo JText::_('COM_EASYBLOG_ADSENSE_DISPLAY_NOTE'); ?>

						 <br /><br />
						 <pre>{eblogads} <br /> -- or -- <br /> {eblogads right} <br /> -- or -- <br /> {eblogads left} <br /></pre>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>