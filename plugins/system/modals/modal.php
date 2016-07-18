<?php
/**
 * @package         Modals
 * @version         6.2.9PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<?php if (JFactory::getApplication()->input->get('iframe')) : ?>
	<?php
	$this->language  = JFactory::getDocument()->language;
	$this->direction = JFactory::getDocument()->direction;
	?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>"
	      dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body class="contentpane modal">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
	</body>
	</html>
<?php else: ?>
	<?php
	require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
	$parameters = NNParameters::getInstance();
	$config     = $parameters->getPluginParams('modals');
	?>
	<?php if ($config->load_head) : ?>
		<jdoc:include type="head" />
	<?php endif; ?>
	<jdoc:include type="message" />
	<jdoc:include type="component" />
<?php endif; ?>
