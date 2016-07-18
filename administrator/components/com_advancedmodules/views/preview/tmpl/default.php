<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<script>
	var form = window.top.document.adminForm
	var title = form.title.value;

	var alltext = window.top.<?php echo $this->editor->getContent('text') ?>;
</script>

<table class="center" width="90%">
	<tr>
		<td class="contentheading" colspan="2">
			<script>document.write(title);</script>
		</td>
	</tr>
	<tr>
		<td valign="top" height="90%" colspan="2">
			<script>document.write(alltext);</script>
		</td>
	</tr>
</table>
