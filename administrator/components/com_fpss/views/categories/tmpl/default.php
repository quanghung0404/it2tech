<?php
/**
 * @version		$Id: default.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
  <table class="fpssAdminTableFilters table">
		<tr>
			<td class="fpssAdminTableFiltersSearch">
				<?php echo JText::_('FPSS_FILTER'); ?>
				<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" title="<?php echo JText::_('FPSS_FILTER_BY_NAME'); ?>" />
				<button id="fpssSubmitButton"><?php echo JText::_('FPSS_GO'); ?></button>
				<button id="fpssResetButton"><?php echo JText::_('FPSS_RESET'); ?></button>
			</td>
			<td class="fpssAdminTableFiltersSelects">
				<?php echo $this->filters['published']; ?>
				<?php if(isset($this->filters['language'])): ?>
				<?php echo $this->filters['language']; ?>
				<?php endif; ?>
			</td>
		</tr>
  </table>
  <table id="fpssCategoriesList" class="adminlist table table-striped">
    <thead>
			<tr>
				<?php if(version_compare(JVERSION, '3.0', 'ge')): ?>
                <th width="1%" class="center hidden-phone">
                    <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'category.ordering', @$this->filters['orderingDir'], @$this->filters['ordering'], null, 'asc', 'FPSS_ORDER'); ?>
                </th>
                <?php endif; ?>
				<th class="fpssCenter"><input id="jToggler" type="checkbox" name="toggle" value="" /></th>
				<th class="fpssLeft"><?php echo JHTML::_('grid.sort', 'FPSS_NAME', 'category.name', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
				<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
				<th>
					<?php echo JHTML::_('grid.sort', 'FPSS_ORDER', 'category.ordering', @$this->filters['orderingDir'], @$this->filters['ordering']); ?>
					<?php if ($this->orderingFlag) {echo JHTML::_('grid.order', $this->rows);} ?></th>
				<?php endif; ?>
				<th class="fpssCenter"><?php echo JHTML::_('grid.sort', 'FPSS_PUBLISHED', 'category.published', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
				<th class="fpssCenter"><?php echo JHTML::_('grid.sort', 'FPSS_NUMBER_OF_SLIDES', 'numOfSlides', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
				<th class="fpssCenter hidden-phone"><?php echo JText::_('FPSS_VIEW_SLIDES'); ?></th>
				<th class="fpssCenter hidden-phone"><?php echo JHTML::_('grid.sort', 'FPSS_ID', 'category.id', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
			</tr>
    </thead>
    <tbody class="fpssSortable">
			<?php foreach($this->rows as $key=>$row): ?>
			<tr class="row<?php echo(($key + 1) % 2); ?>">
				
				<?php if(version_compare(JVERSION, '3.0', 'ge')): ?>
                <td class="order center hidden-phone">
                <?php if($row->canChange): ?>
                    <span class="sortable-handler <?php echo ($this->orderingFlag) ? '' : 'inactive tip-top' ;?>" title="<?php echo ($this->orderingFlag) ? '' :JText::_('JORDERINGDISABLED');?>" rel="tooltip"><i class="icon-menu"></i></span>
                    <input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
                <?php else: ?>
                     <span class="sortable-handler inactive" ><i class="icon-menu"></i></span>
                <?php endif; ?>
                </td>
                <?php endif; ?>
				<td class="fpssCenter"><?php echo JHTML::_('grid.id', $key, $row->id, false, 'id'); ?></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_fpss&view=category&id='.$row->id); ?>"><?php echo $row->name; ?></a> </td>
				<?php if(version_compare(JVERSION, '3.0', 'lt')): ?>
				<td class="fpssOrder order">
				<?php if($this->orderingFlag): ?>
				<span class="handle hasTip" title="<?php echo JText::_('FPSS_DRAG_N_DROP_CATEGORIES_TO_CHANGE_THEIR_ORDERING'); ?>"></span>
				<?php endif; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php
				if (!$this->orderingFlag)
					echo 'disabled="disabled"';
 ?> />
				</td>
				<?php endif; ?>
				<td class="fpssCenter"><?php echo FPSSHelperHTML::published($row, $key); ?></td>
				<td class="fpssCenter"><?php echo $row->numOfSlides; ?></td>
				<td class="fpssCenter hidden-phone"><?php if($row->numOfSlides): ?><a href="<?php echo JRoute::_('index.php?option=com_fpss&view=slides&catid='.$row->id); ?>"><img alt="<?php echo JText::_('FPSS_VIEW_SLIDES'); ?>" src="<?php echo JURI::base().'components/com_fpss/images/view-slides.png'?>"/></a><?php endif; ?></td>
				<td class="fpssCenter hidden-phone"><?php echo $row->id; ?></td>
			</tr>
			<?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
      </tr>
    </tfoot>
  </table>
  <?php if(version_compare( JVERSION, '1.6.0', 'ge' )): ?>
  	
  <?php if(version_compare( JVERSION, '3.0', 'ge' )): ?>
  	<div class="accordion">
  		<div class="accordion-group">
  			<div class="accordion-heading">
  				<a href="#fpssBatch" class="accordion-toggle" data-toggle="collapse"><?php echo JText::_('FPSS_BATCH_CATEGORIES_OPTIONS'); ?></a>
  			</div>
  			<div id="fpssBatch" class="accordion-body collapse">
  				<div class="accordion-inner">
				  <fieldset class="fpssBatch">
					<p class="fpssNote"><?php echo JText::_('FPSS_BATCH_CATEGORIES_TIP'); ?></p>
					<?php echo JHtml::_('batch.language'); ?>
					<div class="clr"></div>
					<button type="submit" class="btn btn-primary" onclick="Joomla.submitbutton('batch');">
						<?php echo JText::_('FPSS_PROCESS'); ?>
					</button>
					<button type="button" class="btn" onclick="document.id('batch-language-id').value=''">
						<?php echo JText::_('FPSS_CLEAR'); ?>
					</button>
					</fieldset>  					
  				</div>
  			</div>
  		</div>
  	</div>
  <?php else: ?>
  <?php echo JHtml::_('sliders.start', 'panel-sliders', array('useCookie' => '1')); ?>
  <?php echo JHtml::_('sliders.panel', JText::_('FPSS_BATCH_CATEGORIES_OPTIONS'), 'fpssCategoriesBatch'); ?>
  <fieldset class="fpssBatch">
	<p class="fpssNote"><?php echo JText::_('FPSS_BATCH_CATEGORIES_TIP'); ?></p>
	<?php echo JHtml::_('batch.language'); ?>
	<div class="clr"></div>
	<button type="submit" class="btn btn-primary" onclick="Joomla.submitbutton('batch');">
		<?php echo JText::_('FPSS_PROCESS'); ?>
	</button>
	<button type="button" class="btn" onclick="document.id('batch-language-id').value=''">
		<?php echo JText::_('FPSS_CLEAR'); ?>
	</button>
	</fieldset>
  <?php echo JHtml::_('sliders.end'); ?>
  <?php endif; ?>
  
  <?php endif; ?>
  <input type="hidden" name="option" value="com_fpss" />
  <input type="hidden" name="view" value="categories" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->filters['ordering']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['orderingDir']; ?>" />
  <input type="hidden" name="task" value="" />
  <?php echo JHTML::_('form.token'); ?>
</form>