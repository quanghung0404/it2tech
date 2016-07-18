<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$document->addScript($pluginLivePath.'/tmpl/GalleriaWhite/js/behaviour.js');

?>

<div id="sigProGalleriaWhite<?php echo $gal_id; ?>" class="sigProContainer sigProGalleriaWhiteContainer<?php echo $extraWrapperClass; ?>">

	<div class="sigProGalleriaWhitePlaceholderContainer">
		<div class="sigProGalleriaWhitePlaceholder">
			<a href="<?php echo $gallery[0]->sourceImageFilePath; ?>" class="sigProGalleriaWhiteTargetLink<?php echo $extraClass; ?>" rel="<?php echo $relName; ?>" title="<?php echo $gallery[0]->captionDescription.$gallery[0]->downloadLink.$modulePosition; ?>" target="_blank">
				<img class="sigProGalleriaWhiteTargetImg" src="<?php echo $gallery[0]->sourceImageFilePath; ?>" alt="<?php echo JText::_('JW_SIGP_LABELS_08').' '.$gallery[0]->filename; ?>" title="<?php echo JText::_('JW_SIGP_LABELS_08').' '.$gallery[0]->filename; ?>" />
			</a>
			<p class="sigProGalleriaWhiteTargetTitle"><?php echo $gallery[0]->captionTitle; ?></p>
		</div>
	</div>

	<ul id="sigProId<?php echo $gal_id; ?>" class="sigProGalleriaWhite<?php echo $extraWrapperClass; ?>">
		<?php foreach($gallery as $count=>$photo): ?>
		<li class="sigProThumb">
			<span class="sigProLinkOuterWrapper">
				<span class="sigProLinkWrapper">
					<a href="<?php echo $photo->sourceImageFilePath; ?>" class="sigProGalleriaWhiteLink sigProLink<?php if($count==0) echo ' sigProLinkSelected'; ?>" style="width:<?php echo $photo->width; ?>px;height:<?php echo $photo->height; ?>px;" title="<?php echo $photo->captionDescription.$photo->downloadLink.$modulePosition; ?>" target="_blank">
						<?php if(($gal_singlethumbmode && $count==0) || !$gal_singlethumbmode): ?>
						<img class="sigProImg" src="<?php echo $transparent; ?>" alt="<?php echo JText::_('JW_SIGP_LABELS_08').' '.$photo->filename; ?>" title="<?php echo JText::_('JW_SIGP_LABELS_08').' '.$photo->filename; ?>" style="width:<?php echo $photo->width; ?>px;height:<?php echo $photo->height; ?>px;background-image:url('<?php echo $photo->thumbImageFilePath; ?>');" />
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
</div>

<?php if(isset($flickrSetUrl)): ?>
<a class="sigProFlickrSetLink" title="<?php echo $flickrSetTitle; ?>" target="_blank" href="<?php echo $flickrSetUrl; ?>"><?php echo JText::_('JW_SIGP_PLG_FLICKRSET'); ?></a>
<?php endif; ?>

<?php if($itemPrintURL): ?>
<div class="sigProPrintMessage">
	<?php echo JText::_('JW_SIGP_PLG_PRINT_MESSAGE'); ?>:
	<br />
	<a title="<?php echo $row->title; ?>" href="<?php echo $itemPrintURL; ?>"><?php echo $itemPrintURL; ?></a>
</div>
<?php endif; ?>
