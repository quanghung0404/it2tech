<?php
/**
 * @package   Foundry
 * @copyright Copyright (C) 2010-2013 Stack Ideas Sdn Bhd. All rights reserved.
 * @license   GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . '/media/foundry/4.0/joomla/framework.php');include(FD40_FOUNDRY_PATH . '/scripts/bootloader' . $this->extension);
?>
<?php echo FD40_FOUNDRY_BOOTCODE; ?>.setup(<?php echo $this->toJSON(); ?>);
