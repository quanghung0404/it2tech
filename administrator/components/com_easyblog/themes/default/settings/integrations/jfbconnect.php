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
                <div class="panel-info">
                    <img style="margin: 0 15px 15px 15px;width: 160px;" align="right" src="<?php echo $this->getPathUri('images/vendors');?>/sourcecoast.png" />
                    <?php echo JText::_('COM_EASYBLOG_JFBCONNECT_INFO'); ?><br /><br />
                    <a href="http://www.shareasale.com/r.cfm?b=495362&u=614082&m=46720&urllink=&afftrack=" class="btn btn-default btn-partners" target="_blank"><?php echo JText::_('COM_EASYBLOG_SIGNUP_WITH_JFBCONNECT');?></a>
                </div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'integrations_jfbconnect_login', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_JFBCONNECT_LOGIN'); ?>
            </div>
		</div>
	</div>
</div>
