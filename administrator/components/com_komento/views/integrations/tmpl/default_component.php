<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="pa-15">
	<table id="kmt-panel" width="100%">
		<tr>
			<td valign="top"style="padding: 10px;">
				<ul id="kmt-items" class="reset-ul kmt-components">
					<?php
						foreach( $this->components as $component )
						{
							echo $this->addComponent( $component );
						}
					?>
				</ul>
			</td>
		</tr>
	</table>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="activechild" id="activechild" value="" />
<input type="hidden" name="task" value="change" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="c" value="integrations" />
</form>
