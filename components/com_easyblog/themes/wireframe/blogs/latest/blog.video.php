<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<p>
<script type="text/javascript">
// @task: Load jwplayer.js and initialize the video player.
EasyBlog.require()
	.script( '<?php echo rtrim( JURI::root() , '/');?>/components/com_easyblog/assets/vendors/jwplayer/jwplayer.js' )
	.done(function($){
		console.log( <?php echo $autoplay ? 'true' : 'false'; ?> );

		jwplayer( 'video-placeholder-<?php echo $uid;?>' ).setup({
			'width': '<?php echo $width;?>',
			'height': '<?php echo $height;?>',
			'file': '<?php echo $url;?>',
			'image': '',
			'controlbar': 'bottom',
			'autostart': <?php echo $autoplay == 'true' ? 'true' : 'false'; ?>,
			'backcolor': '#333333',
			'frontcolor': '#ffffff',
			'modes': [
				{
					type: 'html5'
				},
				{
					type: 'flash',
					src: $.rootPath + '/components/com_easyblog/assets/vendors/jwplayer/player.swf'
				},
				{
					type: 'download'
				}
			]
		});
});
</script>
<div class="eblog-video-player">
	<div id="video-placeholder-<?php echo $uid;?>"></div>
</div>
</p>
