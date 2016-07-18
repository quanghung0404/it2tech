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
<div>
	<div class="mb-5 mr-10 fd-small">
		<?php echo JText::sprintf( 'COM_EASYSOCIAL_ADVANCED_SEARCH_GROUP_MATCHES_NUMBER_ITEM_FOUND', $total ); ?>
	</div>

	<hr />

	<ul class="list-media fd-reset-list list-media-group" data-search-ul data-groups-list>
		<?php foreach( $results as $item ){ ?>
			<?php echo $this->loadTemplate( 'site/advancedsearch/group/default.results.item' , array( 'group' => $item, 'displayOptions' => $displayOptions ) ); ?>
		<?php } ?>

		<li class="fd-reset-list" data-search-pagination data-last-limit="<?php echo $nextlimit; ?>">
			<?php if( $total > FD::themes()->getConfig()->get( 'search_limit' ) ) { ?>
			<a class="btn btn-es-primary btn-stream-updates" href="javascript:void(0);" data-search-loadmore-button><i class="fa fa-refresh"></i>	<?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_LOAD_MORE_ITEMS' ); ?></a>
			<?php } ?>
		</li>

	</ul>

</div>
