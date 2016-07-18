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

// Product Main Image
if (!empty($this->product->images[0])) {
    ?>
    <div class="main-image">
	<?php echo $this->product->images[0]->displayMediaFull('class="medium-image" id="medium-image"', false, "class='modal'", true); ?>
    </div>
<?php } // Product Main Image END ?>

<?php
// Showing The Additional Images
// if(!empty($this->product->images) && count($this->product->images)>1) {
if (!empty($this->product->images) and count ($this->product->images)>1) {
    ?>
    <div class="additional-images">
	<?php
	// List all Images
	if (count($this->product->images) > 0) {
	    foreach ($this->product->images as $image) {
		echo '<div class="floatleft">' . $image->displayMediaThumb('class="product-image"', true, 'class="modal"', true, true) . '</div>'; //'class="modal"'
	    }
	}
	?>
        <div class="clear"></div>
    </div>
<?php
} // Showing The Additional Images END ?>