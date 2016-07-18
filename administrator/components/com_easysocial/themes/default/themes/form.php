<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="themeForm" method="post" action="index.php" id="adminForm">
<div class="row">

	<div class="col-md-8" data-theme-settings>
		<?php echo $theme->form;?>
	</div>

	<div class="col-md-4">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_THEMES_ABOUT' );?></b>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td width="25%"><?php echo JText::_( 'COM_EASYSOCIAL_THEMES_VERSION' );?>:</td>
							<td>
								<?php echo $theme->version; ?>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_( 'COM_EASYSOCIAL_THEMES_AUTHOR' ); ?>:</td>
							<td>
								<a href="mailto:<?php echo $theme->email;?>"><?php echo $theme->author;?></a>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_( 'COM_EASYSOCIAL_THEMES_WEBSITE' ); ?>:</td>
							<td>
								<a href="<?php echo $theme->website;?>" target="_blank"><?php echo $theme->website;?></a>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_( 'COM_EASYSOCIAL_THEMES_CREATED' ); ?>:</td>
							<td>
								<?php echo $this->html( 'string.date' , $theme->created , 'd/m/Y' );?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="activeTab" data-tab-active />
<input type="hidden" name="<?php echo FD::token();?>" value="1" />
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="themes" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="element" value="<?php echo $theme->element;?>" />
</form>
