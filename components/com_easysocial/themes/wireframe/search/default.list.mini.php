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

$last_type = '';
?>
<div class="es-search-result">
<?php if( $data ) { ?>
	<?php foreach( $data as $group => $items) { ?>
		<div class="search-blk">
			<div class="search-blk-bd">
				<ul class="search-result-list" data-nav-search-ul>
					<?php
						if( $items )
						{
							foreach( $items as $item )
							{
								echo $this->loadTemplate( 'site/search/default.item.mini' , array( 'item' => $item ) );
							}
						}
					?>

				</ul>
			</div>
		</div>
	<?php } //end foreach ?>
	<div class="search-footer">
		<div class="text-center fd-small muted">
			<?php echo JText::sprintf( 'COM_EASYSOCIAL_SEARCH_NUMBER_ITEM_FOUND_TOOLBAR', $total ); ?>
		</div>
		<div class="text-center fd-small mt-10">
			<?php
				$linkOptions = array('q' => urlencode($keywords));
				if (isset($filters) && $filters) {
					for($i = 0; $i < count($filters); $i++) {
						$linkOptions['filtertypes[' . $i . ']'] = $filters[$i];
					}
				}
				$searchLink = FRoute::search($linkOptions);
			?>
			<a href="<?php echo $searchLink; ?>">
				<?php echo JText::_('COM_EASYSOCIAL_SEARCH_VIEW_ALL_RESULTS'); ?>
			</a>
		</div>
		<?php if ($showadvancedlink) { ?>
		<div class="text-center fd-small">
			<?php echo JText::sprintf( 'COM_EASYSOCIAL_ADVANCED_SEARCH_TRY_ADVANCED_SEARCH', FRoute::search( array( 'layout' => 'advanced' ) ) ); ?>
		</div>
		<?php } ?>
	</div>

<?php } else { ?>
		<div class="search-empty text-center">
			<?php echo JText::_('COM_EASYSOCIAL_SEARCH_NO_RECORDS_FOUND'); ?>

			<?php if ($showadvancedlink) { ?>
			<div class="fd-small">
			<?php echo JText::sprintf( 'COM_EASYSOCIAL_ADVANCED_SEARCH_TRY_ADVANCED_SEARCH', FRoute::search( array( 'layout' => 'advanced' ) ) ); ?>
			</div>
			<?php } ?>
		</div>

<?php } ?>
</div>
