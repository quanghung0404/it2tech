<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="sigTopWhiteFix"></div>
<div class="sigMiddleWhiteFix"></div>

<div id="sigPro" class="J<?php echo $this->version; ?>">
	<div class="sigProHeader sigLight">
		<div class="sigProTopRow"></div>
		<div class="sigProLogo sigFloatLeft">
			<i class="hidden"><?php echo JText::_('COM_SIGPRO'); ?></i>
		</div>
		<span class="sigMenuVersion"><?php echo JText::_('COM_SIGPRO_VERSION'); ?></span>
		<?php echo $this->menu; ?>
	</div>
	
	<div class="sigProToolbar sigTransition sigBoxSizing sigSlidingItem"> 
  		<div class="sigProUpperToolbar sigInformationToolbar">
  			<h3 class="sigProPageTitle sigPurple"><?php echo strip_tags($this->title); ?></h3>
		</div>
	</div>
	
	<?php echo $this->sidebar; ?>
	
	<div id="sigProMediaManager" class="sigTransition sigSlidingItem"></div>
</div>
<div class="sigBtmWhiteFix"></div>