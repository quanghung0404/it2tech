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

// $mapUrl = "http://www.google.com/maps?q=" . urlencode($post->address) . "&@" . $post->latitude . "," . $post->longitude . "&ll=" . $post->latitude . "," . $post->longitude;
$mapUrl = "http://www.google.com/maps?q=" . $post->latitude . "," . $post->longitude . "&ll=" . $post->latitude . "," . $post->longitude;

?>
<a class="eb-entry-location-map"
    target="_BLANK"
	href="<?php echo $mapUrl; ?>"
	style="background-image:url(https://maps.googleapis.com/maps/api/staticmap?center=<?php echo $post->latitude;?>,<?php echo $post->longitude;?>&language=<?php echo $language; ?>&maptype=<?php echo strtolower($type);?>&zoom=<?php echo $defaultZoom;?>&size=1280x1280&sensor=true&markers=color:red|label:S|<?php echo $post->latitude;?>,<?php echo $post->longitude;?>);height:<?php echo $this->config->get('main_locations_blog_map_height');?>px;"></a>
<a class="eb-entry-location-address row-table" href="<?php echo $mapUrl; ?>" target="_BLANK">
	<div class="col-cell"><i class="fa fa-map-marker text-muted"></i></div>
	<div class="col-cell">
		<?php echo $post->address;?>
	</div>
</a>
