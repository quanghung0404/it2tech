<?php
/**
 * @version     $Id$
 * @package     JSN ImageShow
 * @subpackage  ThemePile
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );
if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}
$i = 1;
$directory = JPATH_PLUGINS . DS . 'jsnimageshow' . DS . 'themepile' . DS . 'assets' . DS . 'images' . DS . 'thumb' . DS;
$path = '../plugins/jsnimageshow/themepile/assets/images/thumb/';
$type = array(1 => 'jpg', 2 => 'jpeg', 3 => 'png', 4 => 'gif');
if ($handle = opendir($directory)) : ?>
<div class='jsn-themepile-wrapper'><ul class='photopile'>
    <?php while (false !== ($entry = readdir($handle))) {
        $ext = explode(".",$entry);
        if ($entry != "." && $entry != ".." && in_array($ext[1],$type)) {
                ?>
                <li id="<?php echo $i; ?>" class="jsn-themepile-box jsn-themepile-image">
                    <a href="<?php echo $path . $entry; ?>">
                        <img id="img_<?php echo $i; ?>" src="<?php echo $path . $entry; ?>" alt="<?php echo $i; ?>"/>
                    </a>
                </li>
            <?php
            $i++;
        }
    }?>
    </ul></div>
    <?php
    closedir($handle);
endif;
?>
