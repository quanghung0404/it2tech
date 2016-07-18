<?php
/**
 * @version    $Id: default_theme_config.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
	(function($){
		$(document).ready(function () {
			var objISImageShow = new $.JQJSNISImageShow();
			objISImageShow.showHintText();
			});
		})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
<div id="jsn-theme-details" class="jsn-bootstrap">
	<form action="index.php?option=com_imageshow&controller=maintenance"
		method="POST" name="adminForm" id="frm_is_param">
		<div id="jsn-showcase-theme-params">
		<?php echo $this->loadTemplate('theme_parameters'); ?>
		</div>
	</form>
</div>
