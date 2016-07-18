<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-widget widget-videos">
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_('APP_VIDEOS_PROFILE_WIDGET_TITLE_VIDEOS'); ?>
		</div>
		<?php if ($params->get('showcount')) { ?>
		<span class="widget-label">(<?php echo $total;?>)</span>
		<?php } ?>
	</div>
	<div class="es-widget-body">
		<ul class="widget-list fd-nav fd-nav-stacked">
			<?php if( $videos ){ ?>
				<?php for ($i = 0; $i < $limit; $i++) { ?>
					<?php if (!empty($videos[$i])) { ?>
						<?php $video = $videos[$i]; ?>
							<li>
							<a href="<?php echo $video->getPermalink();?>" data-es-video="<?php echo $video->id; ?>" class="pl-0 pr-5">
						        <div class="media">
						            <div class="media-object pull-left">

										<img class="widget-video-preview" src="<?php echo $video->getThumbnail();?>" alt="<?php echo $this->html('string.escape' , $video->getTitle());?>" />

						            </div>
						            <div class="media-body">
                						<div class="widget-video-title"><?php echo $video->getTitle(); ?></div>
                					</div>
                    				<div class="widget-video-time"><?php echo $video->getDuration();?></div>
						        </div>
						        </a>
							</li>
					<?php } ?>
				<?php } ?>
			<?php } else { ?>
			<li>
				<div class="fd-small empty">
					<?php echo JText::_( 'APP_VIDEOS_PROFILE_WIDGET_NO_VIDEOS_UPLOADED_YET' ); ?>
				</div>
			</li>
			<?php } ?>
		</ul>

		<?php if (!empty($videos)) { ?>
		<div>
			<a class="fd-small" href="<?php echo FRoute::videos(array());?>"><?php echo JText::_('APP_VIDEOS_PROFILE_WIDGET_VIEW_ALL');?></a>
		</div>
		<?php } ?>
	</div>
</div>
