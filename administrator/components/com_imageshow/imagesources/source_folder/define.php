<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Image Source Flickr
 * @version $Id: define.php 14818 2012-08-07 11:27:26Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
$jsnImageSourceFolder = array(
	'description' => 'Local image folder',
	'thumb' => 'administrator/components/com_imageshow/imagesources/source_folder/assets/images/thumb-folder.png',
	'sync'	=> true,
	'pagination' => true
);

define('JSN_IS_SOURCEFOLDER', json_encode($jsnImageSourceFolder));
