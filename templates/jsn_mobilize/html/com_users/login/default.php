<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die;

// Load template framework


$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<?php if (!JSNMobilizeTemplateHelper::isJoomla3()): ?>
<div class="com-user <?php echo $this->params->get('pageclass_sfx') ?>">
	<div class="default-login">
	<?php endif; ?>
	<?php
		if ($this->user->get('guest')):
			// The user is not logged in.
			echo $this->loadTemplate('login');
		else:
			// The user is already logged in.
			echo $this->loadTemplate('logout');
		endif;
	?>
	<?php if (!JSNMobilizeTemplateHelper::isJoomla3()): ?>
	</div>
</div><?php endif; ?>