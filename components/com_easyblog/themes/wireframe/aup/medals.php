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
	<?php echo JText::_( 'COM_EASYBLOG_AUP_MEDALS_AWARED' ); ?>:
	<?php foreach( $medals as $medal ){ ?>
		<img src="<?php echo JURI::root();?>components/com_alphauserpoints/assets/images/awards/icons/<?php echo $medal->icon;?>" title="<?php echo $medal->rank;?>" />
	<?php } ?>
	<?php if( count($medals) <= 0 ) { echo JText::_('COM_EASYBLOG_AUP_NO_MEDALS'); } ?>
</div>
