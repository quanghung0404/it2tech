<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div style="line-height: 16px;">
	<?php echo JText::_( 'COM_EASYBLOG_AUP_RANKING' ); ?>:
	<?php if( isset( $rank->id ) ) { ?>
		<img width="16px" src="<?php echo JURI::root();?>components/com_alphauserpoints/assets/images/awards/icons/<?php echo $rank->icon;?>" title="<?php echo $rank->rank;?>" />
		( <?php echo $rank->rank;?> )
	<?php } else { ?>
		<?php echo JText::_( 'COM_EASYBLOG_AUP_NO_RANK' ); ?>
	<?php } ?>
</div>
