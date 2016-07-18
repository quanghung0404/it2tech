<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if(!empty($locations)) { ?>
<!-- Location services -->
<script type="text/javascript">
EasyBlog.require()
.script("location")
.script("ratings")
.done(function($) {

	$(".mod-easyblogpostmap").implement("EasyBlog.Controller.Location.Map", {
		language: "<?php echo $language; ?>",
		zoom: <?php echo $zoom; ?>,
		fitBounds: <?php echo $fitBounds; ?>,
		useStaticMap: false,
		disableMapsUI: <?php echo $mapUi; ?>,
		locations: <?php echo json_encode($locations); ?>
	});

});
</script>

<div id="fd" class="eb eb-mod mod-easyblogpostmap<?php echo $params->get('moduleclass_sfx'); ?>" data-eb-module-postmap>
	<div class="locationMap" style="width:<?php echo $params->get('fluid') ? '100%' : $mapWidth.'px'; ?>; height: <?php echo $mapHeight; ?>px;">
	</div>
</div>
<?php } else { ?>
<div id="fd" class="eb eb-mod mod-easyblogpostmap<?php echo $params->get('moduleclass_sfx') ?>">
	<p class="mod-empty"><?php echo JText::_('MOD_EASYBLOGPOSTMAP_NO_LOCATION_POST_FOUND'); ?></p>
</div>
<?php } ?>
