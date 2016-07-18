<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="tab-pane <?php echo $active=='link' ? 'active' : ''; ?>" id="link" data-quickpost-form data-type="link">
	<form class="es-quick-link form-horizontal">

		<div class="form-group">
			<div class="col-md-12">
				<div class="input-group">
					<input type="text" name="link" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_URL_PLACEHOLDER', true);?>" data-quickpost-link/>
					<span class="input-group-btn">
						<button class="btn btn-default" data-quickpost-crawl-link>
							<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_ADD_LINK'); ?>
							<i class="eb-loader-o size-sm hidden" style="margin: 0 0 0 10px" data-quickpost-crawl-loader></i>
						</button>
					</span>
				</div>
			</div>
		</div>

		<div class="hide" data-quickpost-link-preview>
			<div class="form-group">
				<div class="col-md-12">
					<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_TITLE_PLACEHOLDER', true);?>" data-quickpost-title/>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-12">
					<textarea class="form-control" rows="5" placeholder="<?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_CAPTION_PLACEHOLDER', true);?>" data-quickpost-content></textarea>
				</div>
			</div>
		</div>

		<?php echo $this->output('site/dashboard/quickpost/form.more'); ?>
	</form>
</div>
