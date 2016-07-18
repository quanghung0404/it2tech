<?php
/**
 * @version		3.1.x
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Assign the main class
	Let's assume that if the image is bigger than 180px in width we should assign a 4 column layout. Else use a 5 column layout.
	If you want to change these classes, consult the template.css file for the naming pattern.
*/
$layoutclass = ($gallery[0]->width >= 200) ? ' large-block-grid-4 ' : ' large-block-grid-5 ';

?>

<ul id="sigProId<?php echo $gal_id; ?>" class="sigProContainer sigProResponsive small-block-grid-2 medium-block-grid-3 <?php echo $layoutclass.$singleThumbClass.$extraWrapperClass; ?>">
	<?php foreach($gallery as $count=>$photo): ?>
	<li class="sigProThumb"<?php if($gal_singlethumbmode && $count>0) echo ' style="display:none !important;"'; ?>>
		<span class="sigProLinkOuterWrapper">
			<span class="sigProLinkWrapper">
				<a href="<?php echo $photo->sourceImageFilePath; ?>" class="sigProLink<?php echo $extraClass; ?>" style="width: 100%; padding-bottom:<?php echo ($photo->height / $photo->width * 100); ?>%;" rel="<?php echo $relName; ?>[gallery<?php echo $gal_id; ?>]" title="<?php echo $photo->captionDescription.$photo->downloadLink.$modulePosition; ?>" data-fresco-caption="<?php echo $photo->captionDescription.$photo->downloadLink.$modulePosition; ?>" target="_blank"<?php echo $customLinkAttributes; ?>>
					<?php if(($gal_singlethumbmode && $count==0) || !$gal_singlethumbmode): ?>
					<img class="sigProImg" src="<?php echo $transparent; ?>" alt="<?php echo JText::_('JW_SIGP_LABELS_08').' '.$photo->filename; ?>" title="<?php echo JText::_('JW_SIGP_LABELS_08').' '.$photo->filename; ?>" style="background-image:url(<?php echo $photo->thumbImageFilePath; ?>); background-repeat: no-repeat; background-size: cover;" />
					<?php endif; ?>

					<?php if($gal_captions): ?>
					<span class="sigProPseudoCaption"><b><?php echo $photo->captionTitle; ?></b></span>
					<span class="sigProCaption" title="<?php echo $photo->captionTitle; ?>"><?php echo $photo->captionTitle; ?></span>
					<?php endif; ?>
				</a>
			</span>
		</span>
	</li>
	<?php endforeach; ?>
	<li class="sigProClear">&nbsp;</li>
</ul>

<?php if(isset($flickrSetUrl)): ?>
<a class="sigProFlickrSetLink" title="<?php echo $flickrSetTitle; ?>" target="_blank" href="<?php echo $flickrSetUrl; ?>">
	<?php echo JText::_('JW_SIGP_PLG_FLICKRSET'); ?>
</a>
<?php endif; ?>

<?php if($itemPrintURL): ?>
<div class="sigProPrintMessage">
	<?php echo JText::_('JW_SIGP_PLG_PRINT_MESSAGE'); ?>:
	<br />
	<a title="<?php echo $row->title; ?>" href="<?php echo $itemPrintURL; ?>"><?php echo $itemPrintURL; ?></a>
</div>
<?php endif; ?>
