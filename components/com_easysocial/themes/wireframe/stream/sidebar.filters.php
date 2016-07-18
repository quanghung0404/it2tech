<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-widget es-widget-borderless">
	<div class="es-widget-head">
		<?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_FILTERS' );?>

		<a class="pull-right fd-small" href="<?php echo FRoute::stream( array( 'layout' => 'form' ) );?>">
			<i class="icon-es-add"></i> <?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_ADD_FILTER' ); ?>
		</a>
	</div>

	<div class="es-widget-body">
		<ul class="fd-nav fd-nav-stacked feed-items" data-stream-feeds>

			<?php if( count( $items ) > 0 ) { ?>
				<?php foreach( $items as $item ) { ?>
					<li class="<?php echo ( $filter->id == $item->id ) ? 'active' : '' ?>"
						data-sidebar-item
						data-id="<?php echo $item->id; ?>"
						data-url="<?php echo FRoute::stream( array( 'layout' => 'form', 'id' => $item->id ) );?>"
						data-title="<?php echo $this->html( 'string.escape' , $item->title ) . ' - ' . JText::_( 'Filter' , true ); ?>"
					>
						<a href="javascript:void(0);">
							<i class="icon-es-aircon-document mr-5"></i> <?php echo $this->html( 'string.escape' , $item->title );?>
						</a>
					</li>
				<?php } ?>

			<?php } else { ?>
				<li> <?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_EMPTY_LIST' ); ?></li>
			<?php } ?>

		</ul>
	</div>

</div>


