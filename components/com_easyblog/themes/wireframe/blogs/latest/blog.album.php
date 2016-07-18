<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$config = EasyBlogHelper::getConfig();
?>
<script type="text/javascript">
EasyBlog.require()
.library( 'masonry' , 'imagesloaded' , 'fancybox' , 'fancybox/thumbs' )
.stylesheet('fancybox/default', 'fancybox/thumbs')
.done(function($){

	var container 	= $( '#gallery-<?php echo $uid;?>' );

	container.imagesLoaded(function(){
		container.masonry({
			itemSelector : '.gallery-item',
			isRTL: false
		});
	});

	$( '.gallery-thumb-<?php echo $uid;?>' ).fancybox({
		prevEffect	: 'none',
		nextEffect	: 'none',
		helpers	: {

			<?php if (!$config->get( 'main_media_show_lightbox_caption')) { ?>
			title: null,
			<?php } ?>

			overlay	: {
				opacity : 0.8,
				css : {
					'background-color' : '#000'
				}
			},
			thumbs	: {
				width	: 50,
				height	: 50
			}
		}
	});
});
</script>
<div class="blog-gallery-wrap mtm" id="gallery-<?php echo $uid;?>">
	<?php foreach( $images as $image ){ ?>
	<div class="gallery-item">
		<a title="<?php echo $this->escape( $image->title );?>" class="gallery-thumb-item gallery-thumb-<?php echo $uid;?> thumb-link" href="<?php echo $image->original;?>" rel="gallery-thumb-<?php echo $uid;?>"><img src="<?php echo $image->thumbnail;?>" width="128" /></a>
	</div>
	<?php } ?>
</div>
