<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$genres = $this->show_genre_tree($this->items,0,array());
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
  
    <table class="adminlist table table-striped">
      <thead>
        <tr>
          <th class="hidden-phone" width="5"> <?php echo JText::_( 'ID' ); ?> </th>
          <th class="hidden-phone" width="20">  <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
          </th>
          <th> <?php echo JText::_( 'Genre' ); ?> </th>
          <th> <?php echo JText::_( 'Parent genres' ); ?> </th>
          <th class="hidden-phone"> <?php echo JText::_( 'Genre Path' ); ?> </th>
        </tr>
      </thead>
      <?php

	echo $genres;
	?>
    </table>
  
  <input type="hidden" name="option" value="com_muscol" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="genre" />
</form>
