<?php
defined('_JEXEC') or die('Restircted access');

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

require_once (dirname(__FILE__).DS.'helper.php');

$api_key = $params->get('api_key');
$width = $params->get('width');
$height = $params->get('height');
$zoom = $params->get('zoom');
list($cLat, $cLng) = preg_split('/:/', $params->get('centerPoint'));
list($p1_lat, $p1_lng, $p1_info) = preg_split('/:/', $params->get('point1'));
list($p2_lat, $p2_lng, $p2_info) = preg_split('/:/', $params->get('point2'));
list($p3_lat, $p3_lng, $p3_info) = preg_split('/:/', $params->get('point3'));

require(JModuleHelper::getLayoutPath('mod_gmap'));
