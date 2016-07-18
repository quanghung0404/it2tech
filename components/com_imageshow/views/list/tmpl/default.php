<?php
/**
 * @version    $Id$
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if (!count($this->showlists))
{
	echo  $this->objUtils->displayShowlistMissingMessage();
	return false;
}

if (!count($this->showcase))
{
	echo  $this->objUtils->displayShowcaseMissingMessage();
	return false;
}

$thumbDimensionDimension = $this->menuParams->get('thumb_dimension', '250,150');
$thumbDimensionDimension = @explode(',', $thumbDimensionDimension);
$ThumbWidth		= (int) @$thumbDimensionDimension[0];
$ThumbHeight	= (int) @$thumbDimensionDimension[1];
$css = '#jsn_is_list_wapper .jsn-is-list-thumbnail {
	width: '.$ThumbWidth.'px;
}'."\n" ;

$css .= '#jsn_is_list_wapper .jsn-is-list-thumbnail .item-thumb-loading-container .item-thumb-img {
	width: '.$ThumbWidth.'px;
	height: '.$ThumbHeight.'px;
}'."\n" ;

$css .= '#jsn_is_list_wapper .jsn-is-list-thumbnail .item-thumb-loading-container .item-thumb-img span {
	margin-top: '.(round($ThumbHeight/2) - 20).'px;
}'."\n" ;
$this->document->addStyleDeclaration($css);
?>
<div id="jsn_is_list_container">
	<div id="jsn_is_list_wapper">
    <?php if ($this->menuParams->get('show_page_heading', 1)) { ?>
    	<h1 class="componentheading<?php echo $this->escape($this->menuParams->get('pageclass_sfx')); ?>"><?php echo $this->escape($this->menuParams->get('page_title')); ?></h1>
    <?php } ?>
		<?php if (count($this->showlists)) { ?>
			<?php for ($i = 0, $count = count($this->showlists); $i < $count;$i++) {
				$item 		= $this->showlists[$i];
				$dataObj 	= $this->objJSNShowlist->getShowlist2JSON(JURI::base(), $item->showlist_id);
				$images  	= @$dataObj->showlist->images->image;
				@shuffle($images);
				if (@$images[0]->image == '')
				{
					$link = "javascript: void(0);";
				}
				else
				{
					$link = JRoute::_('index.php?option=com_imageshow&view=show&showlist_id='.$item->showlist_id.'&showcase_id='.$this->showcase->showcase_id.'&show_breadcrumbs=1');
				}

			?>
				<div class="jsn-is-list-thumbnail">
					<div class="item-thumb-bg <?php echo ((@$images[0]->image == '') ? 'item-thumb-bg-empty' : ''); ?>">
						<a href="<?php echo $link; ?>" class="view_gallery">
							<span class="item-thumb-loading-container">
								<span class="item-thumb-img" style="background-image: url(<?php echo @$images[0]->image; ?>)">
									<?php
										if (@$images[0]->image == '')
										{
											echo '<span>'.JText::_('SITE_LIST_EMPTY_GALLERY').'</span>';
										}
									?>
								</span>
							</span>
						</a>
					</div>
					<div class="jsn-is-gallery-text">
						<?php if ((int) $this->menuParams->get('show_title', 1)) { ?>
							<h2><?php echo $item->showlist_title; ?></h2>
						<?php } ?>
						<?php

							if ((int) $this->menuParams->get('show_description', 1))
							{
								if ($item->description != '')
								{
									if ((int) $this->menuParams->get('description_word_limitation', 15) == 0)
									{
										echo '<p>' . $item->description . '</p>';
									}
									else
									{
										echo '<p>' . $this->objUtils->wordLimiter($item->description, (int) $this->menuParams->get('description_word_limitation', 15)) . '</p>';
									}
								}
							}

							if ((int) $this->menuParams->get('show_view_gallery_link', 1))
							{
								echo '<p><a href="' . $link . '">' . JText::_('JSN_IMAGESHOW_MORE') . '</a></p>';
							}

						?>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>