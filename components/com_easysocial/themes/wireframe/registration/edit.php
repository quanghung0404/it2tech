<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form method="post">
<script>
// jQuery(function()
// {
//     jQuery('.panel-title').click(function () {
//         sQuery( this ).toggleClass('hidden');
//         sQuery( this ).next(' table ').toggle();
//     });
// });
</script>
<div id="ezs-page" class="profile-edit mtl">
    <h1 class="page-title reset-h"><?php echo JText::_( 'Edit Profile' );?></h1>
    <div id="ezs-main">
		<?php foreach( $this->fields as $group ) { ?>
        <div class="ezs-panel">
		    <div class="panel-title"><?php echo JText::_( $group->title ); ?> <span class="fd-small">- <?php echo $group->description;?></span></div>
            <?php if( $group->childs ) { ?>
		    <table class="table-form reset-table" border="2" cellspacing="0" cellpadding="0">
                <tbody>
    		        <?php foreach( $group->childs as $field ){ ?>
    		        <tr>
    		            <td class="label"><label><?php echo JText::_( $field->title );?> :</label></td>
    		            <td class="value">
                            <div class="hasTips">
                            	<?php echo $field->output; ?>
                                <?php echo FD::get( 'Tooltips' , $field->title , $field->description ); ?>
                            </div>
                        </td>
    		        </tr>
    		        <?php } ?>
                </tbody>
		    </table>
            <?php } ?>
        </div>
		<?php } ?>
    </div>
</div>
<input type="hidden" name="cid" value="<?php echo $this->cid ?>">
<button>submit</button>
</form>
