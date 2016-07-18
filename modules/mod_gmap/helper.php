<?php
defined('_JEXEC') or die('Restricted access');

class modGMapHelper {
        function getParams(&$params) {
		$params->def('api_key', 'ABQIAAAARiXnQpONm0tObWWjCAo2hxRkbdGlYmNOYio6lIyibVzoO-fg5xSD5t4M4X7wpN6eG6VcWHUtqnbFYw');
		$params->def('width', '500');
		$params->def('height', '300');
		$params->def('zoom', '7');
		$params->def('centerPoint', '40.169997:44.52');
		$params->def('point1', '40.169997:44.52:Yerevan, Armenia');
		$params->def('point2', '38.895111:-77.036667:Washington D.C.');
		$params->def('point3', '40.716667:-74:New York City');

        	return $params;
	}
}