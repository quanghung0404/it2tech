<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//PRO
?>
<script>
	ES_Slides.prototype.addNew 
= 
function ()
 {
		var active = this.findWhere({
			active: true
		});
		var newIndex = active ? active.get('index') + 0.5 : 0;
		this.add(_({
			index: newIndex
		}).defaults(ES_Slide.NEW_SLIDE_DEFAULTS)).set({
			active: true,
		})
	};
</script>
