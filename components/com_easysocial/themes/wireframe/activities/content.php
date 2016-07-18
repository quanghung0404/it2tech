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

$filterType = isset( $filtertype ) ? $filtertype : 'all';
?>
<div class="es-snackbar socialActivities">
	<div data-activities-es-stream-content-title><?php echo $title; ?></div>
</div>

<div data-activities-content>
	<?php if( $filterType == 'hiddenapp' ) { ?>
		<?php echo $this->loadTemplate( 'site/activities/default.activities.hiddenapp' , array( 'apps' => $activities ) ); ?>
	<?php } else if( $filterType == 'hiddenactor' ) { ?>
		<?php echo $this->loadTemplate( 'site/activities/default.activities.hiddenactor' , array( 'actors' => $activities ) ); ?>
	<?php } else { ?>
		<?php echo $this->loadTemplate( 'site/activities/content.items' , array( 'activities' => $activities , 'nextlimit' => $nextlimit, 'active' => $active ) ); ?>
	<?php } ?>
</div>
