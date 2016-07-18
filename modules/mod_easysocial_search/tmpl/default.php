<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="es mod-es-search module-search<?php echo $suffix;?>">
	<div class="mod-bd">
		<div class="es-widget">
			<form action="<?php echo JRoute::_('index.php');?>" method="post">

				<div class="fd-navbar-search" data-mod-search data-showadvancedlink="<?php echo $params->get('showadvancedlink', 1); ?>">
					<input type="text" name="q" class="form-control input-sm search-input" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_('MOD_EASYSOCIAL_SEARCH_PHASE', true );?>" />
				</div>

				<?php if (isset($filterTypes) && $filterTypes) { ?>
				<div class="es-mod-filter dropdown pull-right" data-nav-search-filter>
					<a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" data-filter-button>
						<i class="fa fa-cog"></i>
					</a>
					<ul class="es-mod-dropdown fd-reset-list dropdown-menu">
						<li class="es-navbar-dropdown-head">
							<div class="es-filter-head">
								<div><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESC');?></div>
							</div>

							<div class="es-filter-help">
								<div class="col-cell">
									<div class="select-all">
										<a href="javascript:void(0);" data-filter-selectall><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_SELECT_ALL'); ?></a>
									</div>
								</div>

								<div class="col-cell">
									<div class="deselect-all">
										<a href="javascript:void(0);" data-filter-deselectall><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESELECT_ALL'); ?></a>
									</div>
								</div>
							</div>
						</li>
						<?php
							$count = 0;
							foreach($filterTypes as $fType) {
								$typeAlias = $fType->id . '-' . $fType->title;
						?>
						<li>
							<div class="es-checkbox">
								<input id="mod-search-type-<?php echo $count;?>"
										type="checkbox"
										name="filtertypes[]"
										value="<?php echo $typeAlias; ?>"
										<?php echo (isset($fType->checked) && $fType->checked) ? ' checked="true"' : ''; ?>
										data-search-filtertypes />
								<label for="mod-search-type-<?php echo $count;?>">
									<?php echo $fType->displayTitle;?>
								</label>
							</div>
						</li>
					<?php
							$count++;
						}
					?>
					</ul>
				</div>
				<?php } ?>

				<input type="hidden" name="Itemid" value="<?php echo FRoute::getItemId('search');?>" />
				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="search" />
				<input type="hidden" name="task" value="query" />
				<?php echo $modules->html( 'form.token' );?>
			</form>

			<?php if( $params->get('showadvancedlink', 1) ) { ?>
			<div class="mt-5 mr-10 fd-cf">
				<a class="pull-right fd-small" href="<?php echo FRoute::search( array( 'layout' => 'advanced' ) ); ?>"><?php echo JText::_('MOD_EASYSOCIAL_SEARCH_ADVANCED_SEARCH');?></a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
