<?php
/**
 * @version    $Id: default_edit_source_profile.php 16077 2012-09-17 02:30:25Z giangnd $
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

$externalSourceID 	= JRequest::getInt('external_source_id');
$sourceType 		= JRequest::getString('source_type');

?>
<script type="text/javascript">
	(function($){
		$(document).ready(function () {
			var objISImageShow = new $.JQJSNISImageShow();
			objISImageShow.showHintText();
			});
		})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
<div id="jsn-image-source-profile-details">
	<div class="jsn-bootstrap">
		<form name="adminForm" id="frm-edit-source-profile" action="index.php"
			method="post">
			<?php echo $this->loadTemplate('profile_' . $sourceType);?>
			<div class="content-center">
				<span class="jsn-source-icon-loading" id="jsn-create-source"></span>
			</div>
		</form>
	</div>
</div>
