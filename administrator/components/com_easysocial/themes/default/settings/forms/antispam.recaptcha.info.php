<?php
/**
* @package		EasySocial
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
<div class="row">
	<div class="col-md-12" style="margin-top: 50px;">
		<p>
			<img src="<?php echo JURI::root();?>media/com_easysocial/images/admin/recaptcha_sample.png" align="left" style="margin-right: 10px;" />
			<?php echo $settings->renderSettingText('Recaptcha Introduction'); ?>
			<br /><br />
			<a href="http://recaptcha.net" class="btn btn-es-success btn-small"><?php echo $settings->renderSettingText('Recaptcha Get'); ?></a>
			<a href="#" class="btn btn-es-primary btn-small"><?php echo $settings->renderSettingText('Recaptcha Documentation'); ?></a>
		</p>
	</div>
</div>
