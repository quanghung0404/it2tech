<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-template" data-name="site/layout/dialog/default">
	<div id="fd" class="eb eb-dialog has-footer">
		<div class="eb-dialog-modal">
			<div class="eb-dialog-header">
				<div class="row-table">
					<div class="col-cell cell-ellipse"><span class="eb-dialog-title"></span></div>
					<div class="col-cell cell-tight eb-dialog-close-button">
						<i class="fa fa-close"></i>
					</div>
				</div>
			</div>
			<div class="eb-dialog-body">
				<div class="eb-dialog-container">
					<div class="eb-dialog-content"></div>
					<div class="eb-hint hint-loading layout-overlay style-gray size-sm">
						<div>
							<i class="eb-hint-icon"><span class="eb-loader-o size-lg"></span></i>
						</div>
					</div>
					<div class="eb-hint hint-failed layout-overlay style-gray size-sm">
						<div>
							<i class="eb-hint-icon fa fa-warning"></i>
							<span class="eb-hint-text">
								<span class="eb-dialog-error-message"><?php echo JText::_('COM_EASYBLOG_DIALOG_UNABLE_TO_LOAD_CONTENTS'); ?></span>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="eb-dialog-footer">
				<div class="row-table">
					<div class="col-cell eb-dialog-footer-content"></div>
				</div>
			</div>
		</div>
	</div>
</div>