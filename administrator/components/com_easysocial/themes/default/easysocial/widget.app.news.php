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
<div class="panel widget-app-news" data-widget-app-news>
	<div class="panel-head row-table">
		<b class="col-cell"><?php echo JText::_( 'COM_EASYSOCIAL_WIDGET_APPS_TITLE_LATEST_NEWS' );?></b>
		<div class="col-cell text-right">
			<a href="http://stackideas.com/apps" class="btn btn-sm btn-es-primary" target="_blank"><i class="fa fa-shopping-cart"></i>&nbsp; <?php echo JText::_( 'COM_EASYSOCIAL_APPS_BROWSER' );?></a>
		</div>
	</div>
	<ul class="panel-list list-unstyled es-items-list es-apps-list" data-widget-news-items>
		<li data-widget-news-placeholder>
			<em><?php echo JText::_( 'COM_EASYSOCIAL_NEWS_FETCHING_FROM_SERVER' ); ?></em>
		</li>
	</ul>
</div>
