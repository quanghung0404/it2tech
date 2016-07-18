<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->category->haschildren) {

// Category and Columns Counter
$iCol = 1;
$iCategory = 1;

// Calculating Categories Per Row
$categories_per_row = VmConfig::get ( 'categories_per_row', 3 );
$category_cellwidth = ' width'.floor ( 100 / $categories_per_row );

// Separator
$verticalseparator = " vertical-separator";
?>

<div class="category-view">

<?php // Start the Output
if ($this->category->children ) {
    foreach ( $this->category->children as $category ) {

	    // Show the horizontal seperator
	    if ($iCol == 1 && $iCategory > $categories_per_row) { ?>
	    <div class="horizontal-separator"></div>
	    <?php }

	    // this is an indicator wether a row needs to be opened or not
	    if ($iCol == 1) { ?>
	    <div class="row">
	    <?php }

	    // Show the vertical separator
	    if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
		    $show_vertical_separator = ' ';
	    } else {
		    $show_vertical_separator = $verticalseparator;
	    }

	    // Category Link
	    $caturl = JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id );

		    // Show Category ?>
		    <div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
			    <div class="spacer">
				    <h2>
					    <a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
					    <?php echo $category->category_name ?>
					    <br />
					    <?php // if ($category->ids) {
						    echo $category->images[0]->displayMediaThumb("",false);
					    //} ?>
					    </a>
				    </h2>
			    </div>
		    </div>
	    <?php
	    $iCategory ++;

	    // Do we need to close the current row now?
	    if ($iCol == $categories_per_row) { ?>
	    <div class="clear"></div>
	    </div>
		    <?php
		    $iCol = 1;
	    } else {
		    $iCol ++;
	    }
    }
}
// Do we need a final closing row tag?
if ($iCol != 1) { ?>
	<div class="clear"></div>
	</div>
<?php
}
?>
</div>
<?php } ?>