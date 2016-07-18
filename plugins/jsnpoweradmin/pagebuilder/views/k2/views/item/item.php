<?php
/**
 * @version		$Id: item.php 1766 2012-11-22 14:10:24Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die;
$item = $data;
$manifest = JSNUtilsXml::loadManifestCache('pagebuilder', 'plugin', 'jsnpoweradmin');
$version = $manifest->version;
?>
<link rel="stylesheet" href="<?php echo JURI::root() . '/plugins/jsnpoweradmin/pagebuilder/assets/css/jsnpa-pagebuilder.css?version=' . $version; ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo JURI::root() . '/plugins/jsnpoweradmin/k2/assets/css/item_item.css?version=' . $version; ?>" type="text/css" />

<input id="article_id" type="hidden" value="<?php echo $item->id; ?>">
<div class="jsn-article-layout">
	<?php $_showCreatedDateClass = $item->params->get('itemDateCreated') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="item-date-created element-switch contextmenu-approved <?php echo $_showCreatedDateClass;?>" id="itemDateCreated">
		<?php echo JHTML::_('date', $item->created , 'D F n, Y g:i a'); ?>
	</div>

	<div>
		<?php $_showItemTitleClass = $item->params->get('itemTitle') ? 'display-default display-item' : 'hide-item'; ?>
		<div class="item-title element-switch contextmenu-approved <?php echo $_showItemTitleClass;?>" id="itemTitle">
			<h1><?php echo $item->title; ?></h1>
		</div>

		<?php if ($item->featured): ?>
		<?php $_showFeaturedClass = $item->params->get('itemFeaturedNotice') ? 'display-default display-item' : 'hide-item'; ?>
		<div class="item-featured-notice element-switch  contextmenu-approved <?php echo $_showFeaturedClass?>" id="itemFeaturedNotice">
		  	<sup>
		  		<?php echo JText::_('K2_FEATURED'); ?>
		  	</sup>
	  	</div>
	  	<?php endif?>
	  	<div class="clearbreak"></div>
	</div>

	<?php $_showAuthorClass = $item->params->get('itemAuthor') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="item-featured-notice element-switch  contextmenu-approved <?php echo $_showAuthorClass?>" id="itemAuthor">
		<span class="item-author">
			<?php echo K2HelperUtilities::writtenBy($item->author->profile->gender); ?>&nbsp;
			<?php if(empty($item->created_by_alias)): ?>
			<a rel="author" href="javascript:void(0)"><?php echo $item->author->name; ?></a>
			<?php else: ?>
			<?php echo $item->author->name; ?>
			<?php endif; ?>
		</span>
	</div>

	<?php
		$_showToolbarClass =
			$item->params->get('itemFontResizer') ||
			$item->params->get('itemPrintButton') ||
			$item->params->get('itemEmailButton') ||
			$item->params->get('itemSocialButton') ||
			$item->params->get('itemVideoAnchor') ||
			$item->params->get('itemImageGalleryAnchor') ||
			$item->params->get('itemCommentsAnchor')
		?  'display-default display-item' : 'hide-item';
	?>

		<ul class="itemToolbar">
			<?php $_showFontResizerClass = $item->params->get('itemFontResizer') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Font Resizer -->
			<li  class="itemFontResizer element-switch  contextmenu-approved <?php echo $_showFontResizerClass?>" id="itemFontResizer">
				<span class="itemTextResizerTitle"><?php echo JText::_('K2_FONT_SIZE'); ?></span>
				<a href="javscript:void(0)" id="fontDecrease">
					<span><?php echo JText::_('K2_DECREASE_FONT_SIZE'); ?></span>
					<img src="<?php echo JURI::root(true); ?>/components/com_k2/images/system/blank.gif" alt="<?php echo JText::_('K2_DECREASE_FONT_SIZE'); ?>" />
				</a>
				<a href="javscript:void(0)" id="fontIncrease">
					<span><?php echo JText::_('K2_INCREASE_FONT_SIZE'); ?></span>
					<img src="<?php echo JURI::root(true); ?>/components/com_k2/images/system/blank.gif" alt="<?php echo JText::_('K2_INCREASE_FONT_SIZE'); ?>" />
				</a>
			</li>

			<?php $_showPrintButtonClass = $item->params->get('itemPrintButton') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Print Button -->
			<li  class="element-switch  contextmenu-approved <?php echo $_showPrintButtonClass?>" id="itemPrintButton">
				<a class="itemPrintLink" rel="nofollow" href="javscript:void(0)" >
					<span><?php echo JText::_('K2_PRINT'); ?></span>
				</a>
			</li>

			<?php $_showEmailButtonClass = $item->params->get('itemEmailButton') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- EMAIL Button -->
			<li  class="element-switch  contextmenu-approved <?php echo $_showEmailButtonClass?>" id="itemEmailButton">
				<a class="itemEmailLink" rel="nofollow" href="javascript:void(0)">
					<span><?php echo JText::_('K2_EMAIL'); ?></span>
				</a>
			</li>

			<?php if(!is_null($item->params->get('socialButtonCode', NULL))) {?>
			<?php $_showSocialButtonClass = $item->params->get('itemSocialButton') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Item Social Button -->
			<li  class="element-switch  contextmenu-approved <?php echo $_showSocialButtonClass?>" id="itemSocialButton">
				<?php echo $item->params->get('socialButtonCode'); ?>
			</li>
			<?php }?>
			<?php if(!empty($item->video)){ ?>
				<?php $_showVideoAnchorClass = $item->params->get('itemVideoAnchor') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Anchor link to item video below - if it exists -->
			<li  class="element-switch  contextmenu-approved <?php echo $_showVideoAnchorClass?>" id="itemVideoAnchor">
				<a class="itemVideoLink k2Anchor" href="javascript:void(0)"><?php echo JText::_('K2_MEDIA'); ?></a>
			</li>
			<?php }?>

			<?php if(!empty($item->gallery)){?>
				<?php $_showImageGalleryAnchorClass = $item->params->get('itemImageGalleryAnchor') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Anchor link to item image gallery below - if it exists -->
			<li  class="element-switch  contextmenu-approved <?php echo $_showImageGalleryAnchorClass?>" id="itemImageGalleryAnchor">
				<a class="itemImageGalleryLink k2Anchor" href="javascript:void(0)"><?php echo JText::_('K2_IMAGE_GALLERY'); ?></a>
			</li>
			<?php }?>

			<?php $_showCommentAnchorClass = $item->params->get('itemCommentsAnchor') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Anchor link to comments below - if enabled -->
			<li  class="element-switch  contextmenu-approved <?php echo $_showCommentAnchorClass?>" id="itemCommentsAnchor">
					<?php if($item->numOfComments > 0){ ?>
					<a class="itemCommentsLink k2Anchor" href="javascript:void(0)">
						<span><?php echo $item->numOfComments; ?></span> <?php echo ($item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
					</a>
					<?php }else{ ?>
					<a class="itemCommentsLink k2Anchor" href="javascript:void(0)">
						<?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
					</a>
					<?php }; ?>
			</li>

		</ul>


	<?php $_showItemRatingClass = $item->params->get('itemRating') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="itemRating element-switch  contextmenu-approved <?php echo $_showItemRatingClass?>" id="itemRating">
		<span><?php echo JText::_('K2_RATE_THIS_ITEM'); ?></span>
		<div class="itemRatingForm">
			<ul class="itemRatingList">
				<li class="itemCurrentRating" id="itemCurrentRating<?php echo $item->id; ?>" style="width:<?php echo $item->votingPercentage; ?>%;"></li>
				<li><a href="#" rel="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
				<li><a href="#" rel="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
				<li><a href="#" rel="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
				<li><a href="#" rel="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
				<li><a href="#" rel="<?php echo $item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
			</ul>
			<div id="itemRatingLog<?php echo $item->id; ?>" class="itemRatingLog"><?php echo $item->numOfvotes; ?></div>
			<div class="clearbreak"></div>
		</div>
		<div class="clearbreak"></div>
	</div>
	<div class="clearbreak"></div>

	<?php if(!empty($item->image)){
		$item->image = str_replace('/administrator/', '/', $item->image);
	?>
	<?php $_showImageClass = $item->params->get('itemImage') ? 'display-default display-item' : 'hide-item'; ?>
	<!-- Item Image -->
	<div class="item-image element-switch  contextmenu-approved <?php echo $_showImageClass?>" id="itemImage">
		<img src="<?php echo $item->image; ?>" alt="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>" style="width:<?php echo $item->imageWidth; ?>px; height:auto;" />
	</div>

	<?php if(!empty($item->image_caption)){?>
		<?php $_showImageCaptionClass = $item->params->get('itemImageMainCaption') ? 'display-default display-item' : 'hide-item'; ?>
		<!-- Image caption -->
		<div class="item-image-caption element-switch  contextmenu-approved <?php echo $_showImageCaptionClass?>" id="itemImageMainCaption">
			<span class="itemImageCaption"><?php echo $item->image_caption; ?></span>
		</div>
	<?php }?>

	<?php if(!empty($item->image_credits)){?>
		<?php $_showImageCreditClass = $item->params->get('itemImageMainCredits') ? 'display-default display-item' : 'hide-item'; ?>
		<!-- Image credits -->
		<div class="item-image-credit element-switch  contextmenu-approved <?php echo $_showImageCreditClass?>" id="itemImageMainCredits">
			<span class="itemImageCredits"><?php echo $item->image_credits; ?></span>
		</div>
		<?php }?>
	<div class="clearbreak"></div>
	<?php }?>


	<div class="item-intro element-switch  contextmenu-approved display-default" id="itemIntroText">
  		<?php 
  			$content = $item->introtext . $item->fulltext;
  			if (JPluginHelper::isEnabled('jsnpoweradmin', 'pagebuilder'))
  			{
	  			$dispatcher = JDispatcher::getInstance();
	  			JPluginHelper::importPlugin('jsnpoweradmin', 'pagebuilder');
	  					
	  			$processedContent = $dispatcher->trigger('onJSNPAPBReplaceContent',  array($content));
	  			$content = str_replace('jsn-element-container', '', $processedContent[0]);
  			}  			
  			echo $content; 
  		?>
	</div>


	<div class="clearbreak"></div>

	<?php if(count($item->extra_fields)){?>
	<!-- Item extra fields -->
	<?php $_showExtraFieldsClass = $item->params->get('itemExtraFields') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="item-extra-fields element-switch  contextmenu-approved <?php echo $_showExtraFieldsClass?>" id="itemExtraFields">
		<h3><?php echo JText::_('K2_ADDITIONAL_INFO'); ?></h3>
		<ul>
			<?php foreach ($item->extra_fields as $key=>$extraField): ?>
			<?php if($extraField->value != ''): ?>
			<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
				<?php if($extraField->type == 'header'): ?>
				<h4 class="itemExtraFieldsHeader"><?php echo $extraField->name; ?></h4>
				<?php else: ?>
				<span class="itemExtraFieldsLabel"><?php echo $extraField->name; ?>:</span>
				<span class="itemExtraFieldsValue"><?php echo $extraField->value; ?></span>
				<?php endif; ?>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	    <div class="clearbreak"></div>
	</div>
	<?php }?>

	<!-- Item Content footer -->
	<?php if(intval($item->modified)!=0){ ?>
	<div class="itemContentFooter">
		<?php $_showHitsClass = $item->params->get('itemHits') ? 'display-default display-item' : 'hide-item'; ?>
		<div class="itemHits element-switch  contextmenu-approved <?php echo $_showHitsClass?>" id="itemHits">
			<?php echo JText::_('K2_READ'); ?> <b><?php echo $item->hits; ?></b> <?php echo JText::_('K2_TIMES'); ?>
		</div>

			<?php $_showModifiedClass = $item->params->get('itemDateModified') ? 'display-default display-item' : 'hide-item'; ?>
		<div class="itemDateModified element-switch  contextmenu-approved <?php echo $_showModifiedClass?>" id="itemDateModified">
			<?php echo JText::_('K2_LAST_MODIFIED_ON'); ?> <?php echo JHTML::_('date', $item->modified , 'D F n, Y g:i a'); ?>
		</div>
	</div>
	<?php }?>
	<div class="clearbreak"></div>

	<div class="item-social-sharing">
		<?php $_showSocialTwitterClass = $item->params->get('itemTwitterButton', 1) ? 'display-default display-item' : 'hide-item'; ?>
		<!-- Image credits -->
		<div class="itemTwitterButton element-switch  contextmenu-approved <?php echo $_showSocialTwitterClass?>" id="itemTwitterButton">
			<a href="javascript:void(0)">Twitter</a>
		</div>

		<?php $_showSocialFacebookClass = $item->params->get('itemFacebookButton', 1) ? 'display-default display-item' : 'hide-item'; ?>
		<!-- Image credits -->
		<div class="itemFacebookButton element-switch  contextmenu-approved <?php echo $_showSocialFacebookClass?>" id="itemFacebookButton">
			<a href="javascript:void(0)">Facebook</a>
		</div>

		<?php $_showSocialGoogleplusClass = $item->params->get('itemGooglePlusOneButton', 1) ? 'display-default display-item' : 'hide-item'; ?>
		<!-- Image credits -->
		<div class="itemGooglePlusOneButton element-switch  contextmenu-approved <?php echo $_showSocialGoogleplusClass?>" id="itemGooglePlusOneButton">
			<a href="javascript:void(0)">Google+</a>
		</div>
	</div>
	<div class="clearbreak"></div>


	<?php $_showCategoryClass = $item->params->get('itemCategory') ? 'display-default display-item' : 'hide-item'; ?>
	<!-- Item category -->
	<div class="itemCategory element-switch  contextmenu-approved <?php echo $_showCategoryClass?>" id="itemCategory">
		<span><?php echo JText::_('K2_PUBLISHED_IN'); ?></span>
		<span class="contextmenu-approved" id="itemCategoryName">
		<a href="javascript:void(0)"><?php echo $item->category->name; ?></a>
		</span>
	</div>

	<?php if(count($item->tags)){ ?>
		<?php $_showTagsClass = $item->params->get('itemTags') ? 'display-default display-item' : 'hide-item'; ?>
	<!-- Item tags -->
	<div class="itemTags element-switch  contextmenu-approved <?php echo $_showTagsClass?>" id="itemTags">
		<span><?php echo JText::_('K2_TAGGED_UNDER'); ?></span>
		  <ul class="itemTags">
		    <?php foreach ($item->tags as $tag): ?>
		    <li><a href="javascript:void(0)"><?php echo $tag->name; ?></a></li>
		    <?php endforeach; ?>
		  </ul>
		  <div class="clearbreak"></div>
	</div>
	<?php }?>

	<?php if( count($item->attachments)){?>
		<?php $_showAttachmentsClass = $item->params->get('itemAttachments') ? 'display-default display-item' : 'hide-item'; ?>
	<!-- Item attachments -->
	<div class="itemAttachments element-switch  contextmenu-approved <?php echo $_showAttachmentsClass?>" id="itemAttachments">
		<span><?php echo JText::_('K2_DOWNLOAD_ATTACHMENTS'); ?></span>
		  <ul class="itemAttachments">
		    <?php foreach ($item->attachments as $attachment){ ?>
		    <li>
			    <a title="<?php echo K2HelperUtilities::cleanHtml($attachment->titleAttribute); ?>" href="javascript:void(0)"><?php echo $attachment->title; ?></a>
			    <?php $_showAttachmentsCounterClass = $item->params->get('itemAttachmentsCounter') ? 'display-default display-item' : 'hide-item'; ?>
			    <span class="itemAttachmentsCounter element-switch  contextmenu-approved <?php echo $_showAttachmentsCounterClass?>" id="itemAttachmentsCounter">(<?php echo $attachment->hits; ?> <?php echo ($attachment->hits==1) ? JText::_('K2_DOWNLOAD') : JText::_('K2_DOWNLOADS'); ?>)</span>
			</li>
		    <?php } ?>
		  </ul>
	</div>
	<?php }?>

	<?php if(empty($item->created_by_alias)){?>
	<?php $_showAuthorBlockClass = $item->params->get('itemAuthorBlock') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="itemAuthorBlock element-switch  contextmenu-approved <?php echo $_showAuthorBlockClass?>" id="itemAuthorBlock">

		<?php if(!empty($item->author->avatar)){?>
			<?php $_showAuthorImageClass = $item->params->get('itemAuthorImage') ? 'display-default display-item' : 'hide-item'; ?>
			<!-- Author image -->
			<div class="itemAuthorImage element-switch  contextmenu-approved <?php echo $_showAuthorImageClass?>" id="itemAuthorImage">
				<img class="itemAuthorAvatar" src="<?php echo $item->author->avatar; ?>" />
			</div>
		<?php }?>

		<div class="itemAuthorInfo">
			<div class="itemAuthorName">
				<?php echo $item->author->name; ?>
			</div>

			<?php if(!empty($item->author->profile->description)){ ?>
				<?php $_showAuthorDescClass = $item->params->get('itemAuthorDescription') ? 'display-default display-item' : 'hide-item'; ?>
				<!-- Author description -->
				<div class="itemAuthorDescription element-switch  contextmenu-approved <?php echo $_showAuthorDescClass?>" id="itemAuthorDescription">
					<p><?php echo $item->author->profile->description; ?></p>
				</div>
			<?php }?>

			<?php if(!empty($item->author->profile->url)){ ?>
				<?php $_showAuthorUrlClass = $item->params->get('itemAuthorURL') ? 'display-default display-item' : 'hide-item'; ?>
				<!-- Author url -->
				<div class="itemAuthorURL element-switch  contextmenu-approved <?php echo $_showAuthorUrlClass?>" id="itemAuthorURL">
					<span class="itemAuthorUrl"><?php echo JText::_('K2_WEBSITE'); ?> <a rel="me" href="javascript:void(0)" target="_blank"><?php echo str_replace('http://','',$item->author->profile->url); ?></a></span>
				</div>
			<?php }?>

			<?php if(!empty($item->author->email)){ ?>
				<?php $_showAuthorEmailClass = $item->params->get('itemAuthorEmail') ? 'display-default display-item' : 'hide-item'; ?>
				<!-- Author email -->
				<div class="itemAuthorEmail element-switch  contextmenu-approved <?php echo $_showAuthorEmailClass?>" id="itemAuthorEmail">
					<span class="itemAuthorEmail"><?php echo $item->author->email ?></span>
				</div>
			<?php }?>
		</div>
		<div class="clearbreak"></div>
	</div>
	<?php }?>

	<?php if(empty($item->created_by_alias) && isset($item->authorLatestItems)){ ?>
		<?php $_showAuthorLastestClass = $item->params->get('itemAuthorLatest') ? 'display-default display-item' : 'hide-item'; ?>
	<!-- Author url -->
	<div class="itemAuthorLatest element-switch  contextmenu-approved <?php echo $_showAuthorLastestClass?>" id="itemAuthorLatest">
		<h3><?php echo JText::_('K2_LATEST_FROM'); ?> <?php echo $item->author->name; ?></h3>
		<ul>
			<?php foreach($item->authorLatestItems as $key=>$_item): ?>
			<li class="<?php echo ($key%2) ? "odd" : "even"; ?>">
				<a href="javascript:void(0)"><?php echo $item->title; ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="clearbreak"></div>
	</div>
	<?php }?>

	<?php if(isset($item->relatedItems)){?>
		<?php $_showItemRelatedClass = $item->params->get('itemRelated') ? 'display-default display-item' : 'hide-item'; ?>
		<div class="itemlist-container" >
			<div class="itemAuthorLatest element-switch  contextmenu-approved <?php echo $_showItemRelatedClass?>" id="itemRelated">
				<h3><?php echo JText::_("K2_RELATED_ITEMS_BY_TAG"); ?></h3>
				<ul>
					<?php foreach($item->relatedItems as $key=>$_item): ?>
					<li class="<?php echo ($key%2) ? "odd" : "even"; ?>">
						<?php $_showRelatedTitleClass = $item->params->get('itemRelatedTitle') ? 'display-default display-item' : 'hide-item'; ?>
						<a class="itemRelTitle element-switch  contextmenu-approved <?php echo $_showRelatedTitleClass?>" href="javascript:void(0)"><?php echo $item->title; ?></a>

						<?php $_showRelatedCategoryClass = $item->params->get('itemRelatedCategory') ? 'display-default display-item' : 'hide-item'; ?>
						<div class="itemRelCat element-switch  contextmenu-approved <?php echo $_showRelatedCategoryClass; ?>"><?php echo JText::_("K2_IN"); ?> <a  href="javascript:void(0)"><?php echo $item->category->name; ?></a></div>

						<?php $_showRelatedAuthorClass = $item->params->get('itemRelatedAuthor') ? 'display-default display-item' : 'hide-item'; ?>
						<div class="itemRelAuthor element-switch  contextmenu-approved <?php echo $_showRelatedAuthorClass;?>"><?php echo JText::_("K2_BY"); ?> <a rel="author"  href="javascript:void(0)"><?php echo $item->author->name; ?></a></div>

						<?php $_showRelatedImagesizeClass = $item->params->get('itemRelatedImageSize') ? 'display-default display-item' : 'hide-item'; ?>
						<img style="width:<?php echo $item->imageWidth; ?>px;height:auto;" class="itemRelImg element-switch  contextmenu-approved <?php echo $_showRelatedImagesizeClass;?>" src="<?php echo $item->image; ?>" alt="<?php K2HelperUtilities::cleanHtml($item->title); ?>" />

						<?php $_showRelatedIntrotextClass = $item->params->get('itemRelatedIntrotext') ? 'display-default display-item' : 'hide-item'; ?>
						<div class="itemRelIntrotext element-switch  contextmenu-approved <?php echo $_showRelatedIntrotextClass;?>"><?php echo $item->introtext; ?></div>

						<?php $_showRelatedFulltextClass = $item->params->get('itemRelatedFulltext') ? 'display-default display-item' : 'hide-item'; ?>
						<div class="itemRelFulltext element-switch  contextmenu-approved <?php echo $_showRelatedFulltextClass;?>"><?php echo $item->fulltext; ?></div>


						<?php $_showRelatedMediaClass = $item->params->get('itemRelatedMedia') ? 'display-default display-item' : 'hide-item'; ?>
						<div class="itemRelMediaEmbedded element-switch  contextmenu-approved <?php echo $_showRelatedMediaClass; ?>"><?php echo $item->video; ?></div>

						<?php $_showRelatedImageGalleryClass = $item->params->get('itemRelatedImageGallery') ? 'display-default display-item' : 'hide-item'; ?>
						<div class="itemRelImageGallery element-switch  contextmenu-approved <?php echo $_showRelatedImageGalleryClass;?>"><?php echo $item->gallery; ?></div>

					</li>
					<?php endforeach; ?>
					<li class="clearbreak"></li>
				</ul>
			</div>
			<div class="clearbreak"></div>
		</div>
	<?php }?>
	<div class="clearbreak"></div>
	<?php if(!empty($item->video)){ ?>

	<?php $_showVideoClass = $item->params->get('itemVideo') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="itemVideo element-switch contextmenu-approved <?php echo $_showVideoClass?>"  id="itemVideo">
		<h3><?php echo JText::_('K2_MEDIA'); ?></h3>
				<?php echo JText::_('JSN_RAWMODE_COMPONENT_K2_COMMENT_FORM_COME');?>
		  <?php if(!empty($item->video_caption)){ ?>
		  <?php $_showVideoCaptionClass = $item->params->get('itemVideoCaption') ? 'display-default display-item' : 'hide-item'; ?>
		  <span class="itemVideoCaption element-switch  contextmenu-approved  <?php echo $_showVideoCaptionClass; ?>" id="itemVideoCaption"><?php echo $item->video_caption; ?></span>
		  <?php }?>

		  <?php if(!empty($item->video_credits)){ ?>
  		  <?php $_showVideoCreditsClass = $item->params->get('itemVideoCredits') ? 'display-default display-item' : 'hide-item'; ?>
		  <span class="itemVideoCredits element-switch  contextmenu-approved  <?php echo $_showVideoCreditsClass; ?>" id="itemVideoCredits"><?php echo $item->video_credits; ?></span>
		  <?php } ?>

		<div class="clearbreak"></div>
	</div>
	<?php }?>
<div class="clearbreak"></div>
	<?php if(!empty($item->gallery)){ ?>
		<?php $_showImageGalleryClass = $item->params->get('itemImageGallery') ? 'display-default display-item' : 'hide-item'; ?>
	<!-- Item image gallery -->

	<div class="itemImageGallery element-switch  contextmenu-approved  <?php echo $_showImageGalleryClass; ?>" id="itemImageGallery">
		<h3><?php echo JText::_('K2_IMAGE_GALLERY'); ?></h3>
		  <?php echo $item->gallery; ?>
	</div>
	<?php } ?>

	<?php if(isset($item->nextLink) || isset($item->previousLink)){ ?>
	  <?php $_showNavigationClass = $item->params->get('itemNavigation') ? 'display-default display-item' : 'hide-item'; ?>
	  <!-- Item navigation -->
	  <div class="itemNavigation element-switch  contextmenu-approved <?php echo $_showNavigationClass; ?>">
	  	<span class="itemNavigationTitle"><?php echo JText::_('K2_MORE_IN_THIS_CATEGORY'); ?></span>

			<?php if(isset($item->previousLink)){ ?>
			<a class="itemPrevious" href="<?php echo $item->previousLink; ?>">
				&laquo; <?php echo $item->previousTitle; ?>
			</a>
			<?php } ?>

			<?php if(isset($item->nextLink)){ ?>
			<a class="itemNext" href="<?php echo $item->nextLink; ?>">
				<?php echo $item->nextTitle; ?> &raquo;
			</a>
			<?php } ?>

	  </div>
	  <?php } ?>

	<div class="itemComments-container">
	<?php $_showCommentClass = $item->params->get('itemComments') ? 'display-default display-item' : 'hide-item'; ?>

		<div class="itemComments blank-context-handler element-switch  contextmenu-approved  <?php echo $_showCommentClass?>" id="itemComments" extvalue="<?php echo (int)$item->params->get('comments')?>">&nbsp;
			<?php if($item->params->get('commentsFormPosition')=='above') { ?>
			<div class="commentsFormPosition element-switch  contextmenu-approved <?php echo $_showCommentClass?>" id="commentsFormPosition" data="above">
				<?php include_once JPATH_ROOT . '/plugins/jsnpoweradmin/k2/forms/item_comments_form.php';?>
			</div>
			<?php }?>

		 	<?php if($item->numOfComments>0){ ?>
		  	<!-- Item user comments -->
		  	<h3 class="itemCommentsCounter">
		  		<span><?php echo $item->numOfComments; ?></span> <?php echo ($item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
		  	</h3>
		  	<ul class="itemCommentsList">
	    		<li>
					<span><?php echo JText::_('JSN_RAWMODE_COMPONENT_K2_COMMENT_FORM_COME');?></span>
	    		</li>
    		</ul>
	    	<?php }?>

			<?php if($item->params->get('commentsFormPosition')=='below') { ?>
			<div class="commentsFormPosition element-switch  contextmenu-approved <?php echo $_showCommentClass?>" id="commentsFormPosition" data="below">
				<?php include_once JPATH_ROOT . '/plugins/jsnpoweradmin/k2/forms/item_comments_form.php';?>
			</div>
			<?php }?>
    	</div>

	</div>
</div>
<input id="jsnpa-afterajax" type="hidden" value="var JSNPAPageBuilder = new $.JSNPAPageBuilder(); JSNPAPageBuilder.initialize();" />
