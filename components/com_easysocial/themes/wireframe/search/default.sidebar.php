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

$routerOptions = array('q' => $query);
?>
<div class="es-widget es-widget-borderless">
	<div class="es-widget-head">
		<?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_FILTER' );?>
	</div>

	<div class="es-widget-body">
		<ul class="fd-nav fd-nav-stacked search-items" data-search-sidebar>

			<li class="active"
				data-sidebar-menu
				data-sidebar-item
				data-type=""
				data-url="<?php echo FRoute::search($routerOptions);?>"
			>
				<a href="javascript:void(0);">
					<i class="fa fa-search mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_BY_ALL' );?>
					<div class="label label-notification pull-right mr-20"></div>
				</a>
			</li>

			<?php if( $types ) { ?>
				<?php foreach( $types as $item ) {

						$lang = JText::_( 'COM_EASYSOCIAL_SEARCH_BY_' . strtoupper( $item->displayTitle ) );
						$typeAlias = $item->id . ':' . $item->title;

						$routerOptions = array('q' => $query);
						$routerOptions['type'] = $typeAlias;

						$routerOptions['filtertypes[0]'] = $typeAlias;
					?>
					<li
						data-sidebar-menu
						data-sidebar-item
						data-type="<?php echo $typeAlias; ?>"
						data-url="<?php echo FRoute::search($routerOptions);?>"
					>
						<a href="javascript:void(0);">
							<i class="<?php echo $item->icon;?>  mr-5"></i> <?php echo (strpos($lang, 'COM_EASYSOCIAL_SEARCH_BY_') !== false) ? ucfirst($item->displayTitle) : $lang ;?>
							<div class="label label-notification pull-right mr-20"></div>
						</a>
					</li>
				<?php } ?>

			<?php } ?>

		</ul>
	</div>

</div>
