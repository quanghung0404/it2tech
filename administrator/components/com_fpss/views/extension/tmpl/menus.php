<?php
/**
 * @version		$Id: menus.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
 ?>
<form action="index.php" method="post"	name="adminForm">
  <table class="fpssAdminTableFilters table">
    <tr>
      <td>
      	<label for="search"><?php echo JText::_('FPSS_FILTER'); ?>:</label>
        <input type="text" name="search" id="search" value="<?php echo $this->filters['search']; ?>" title="<?php echo JText::_('FPSS_FILTER_BY_NAME'); ?>"/>
      	<button id="fpssSubmitButton"><?php echo JText::_('FPSS_GO'); ?></button>
		<button id="fpssResetButton"><?php echo JText::_('FPSS_RESET'); ?></button>
      </td>
      <td class="fpssAdminTableFiltersSelects">
	  	<label for="published"><?php echo JText::_('FPSS_STATE'); ?>:</label>
	  	<?php echo $this->filters['published']; ?>
	  	<label for="menuType"><?php echo JText::_('FPSS_MENU'); ?>:</label>
	  	<?php echo $this->filters['menuType']; ?>
	  </td>
    </tr>
  </table>
  <table class="adminlist table table-stripped">
    <thead>
      <tr>
        <th>#</th>
		<th><?php echo JHTML::_('grid.sort', 'FPSS_NAME', 'name', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
        <th class="fpssCenter"><?php echo JHTML::_('grid.sort', 'FPSS_PUBLISHED', 'published', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
        <th class="fpssCenter"><?php echo JHTML::_('grid.sort', 'FPSS_MENU_TYPE', 'menutype', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
      	<th class="fpssCenter"><?php echo JHTML::_('grid.sort', 'FPSS_ITEM_ID', 'id', @$this->filters['orderingDir'], @$this->filters['ordering']); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
      </tr>
    </tfoot>
    <tbody>
	<?php foreach($this->rows as $key=>$row): ?>
	      <tr class="row<?php echo(($key + 1) % 2); ?>">
	        <td><?php echo $key + 1; ?></td>
			<td><a href="#" onclick="window.parent.jSelectMenu('<?php echo $row->id?>', '<?php echo JString::str_ireplace(array("'", "\""), array("\\'",""), $row->name); ?>');"><?php echo $row->treename; ?></a> </td>
			<td class="fpssCenter"><?php echo $row->published; ?></td>
			<td class="fpssCenter"><?php echo $row->menutype; ?></td>
			<td class="fpssCenter"><?php echo $row->id; ?></td>
	      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <input type="hidden" name="option" value="com_fpss" />
  <input type="hidden" name="view" value="extension" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->filters['ordering']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['orderingDir']; ?>" />
  <input type="hidden" name="task" value="com_menus" />
  <?php echo JHTML::_('form.token'); ?>
</form>