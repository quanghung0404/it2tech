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

$core 		= array( SOCIAL_INDEXER_TYPE_USERS, SOCIAL_INDEXER_TYPE_PHOTOS, SOCIAL_INDEXER_TYPE_LISTS, SOCIAL_INDEXER_TYPE_ALBUMS, SOCIAL_INDEXER_TYPE_EVENTS );
$existingType = $last_type;
?>
<?php if( $data ) { ?>
	<?php foreach( $data as $group => $items) {
		$groupTmpl = ( in_array( $group, $core ) ) ? $group : 'other';
	?>
		<ul class="es-item-grid es-item-grid_1col" data-search-ul>
			<?php
				if( $items )
				{
					foreach( $items as $item )
					{
						echo $this->loadTemplate( 'site/search/default.item.' . $groupTmpl , array( 'item' => $item ) );
					}
				}
			?>
		</ul>
	<?php } //end foreach ?>
<?php } // if (data ) ?>
