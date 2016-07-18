<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$config = FD::config();

// Normalize options
$defaultOptions = array(
	'size'      => $config->get('photos.layout.size'),
	'mode'      => $config->get('photos.layout.pattern')=='flow' ? 'contain' : $config->get('photos.layout.mode'),
	'pattern'   => $config->get('photos.layout.pattern'),
	'ratio'     => $config->get('photos.layout.ratio'),
	'threshold' => $config->get('photos.layout.threshold')
);

if (isset($options)) {
	$options = array_merge_recursive($options, $defaultOptions);
} else {
	$options = $defaultOptions;
}
?>

<div class="es-photos photos-<?php echo count($photos); ?> es-stream-photos pattern-<?php echo $options['pattern']; ?>"
     data-es-photo-group="<?php echo isset($album) && !empty($album) ? 'album:' . $album->id : ''; ?>">

	<?php foreach ($photos as $photo) { ?>
	<div class="es-photo es-stream-photo ar-<?php echo $options['ratio']; ?>">
		<a href="<?php echo $photo->getPermalink();?>"
		   data-es-photo="<?php echo $photo->id; ?>"
		   title="<?php echo $this->html('string.escape', $photo->title . (($photo->caption!=='') ? ' - ' . $photo->caption : '')); ?>">
		   	<u><b data-mode="<?php echo $options['mode']; ?>"
		   	      data-threshold="<?php echo $options['threshold']; ?>">
					<img src="<?php echo $photo->getSource($options['size']); ?>"
						 alt="<?php echo $this->html('string.escape', $photo->title . (($photo->caption!=='') ? ' - ' . $photo->caption : '')); ?>"
						 data-width="<?php echo $photo->getWidth(); ?>"
						 data-height="<?php echo $photo->getHeight(); ?>"
						 onload="window.ESImage ? ESImage(this) : (window.ESImageList || (window.ESImageList=[])).push(this);" />
			</b></u>
		</a>
	</div>
	<?php } ?>
</div>
