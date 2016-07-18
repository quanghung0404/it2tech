<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: preview.php 14473 2012-07-27 09:30:24Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
?>
<div id="jsn-themegrid-container" class="jsn-themegrid-container">
<?php
$i = 1;
$directory	= JPATH_PLUGINS.DS.'jsnimageshow'.DS.'themegrid'.DS.'assets'.DS.'images'.DS.'thumb'.DS;
$path		= '../plugins/jsnimageshow/themegrid/assets/images/thumb/';
$type=Array(1 => 'jpg', 2 => 'jpeg', 3 => 'png', 4 => 'gif');
if ($handle = opendir($directory)) {
	while (false !== ($entry = readdir($handle))) {
		$ext = explode(".",$entry);
		if ($entry != "." && $entry != ".." && in_array($ext[1],$type)) {
			?>
	<div id="<?php echo $i; ?>"
		class="jsn-themegrid-box jsn-themegrid-image">
		<img id="img_<?php echo $i; ?>" src="<?php echo $path.$entry;?>"
			alt="<?php echo $i; ?>" />
	</div>
	<?php
	$i++;
		}
	}
	closedir($handle);
}
?>
</div>
