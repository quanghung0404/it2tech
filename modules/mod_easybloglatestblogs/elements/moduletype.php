<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldModuleType extends JFormField
{

	protected $type = 'ModuleType';

	protected function getInput()
	{
		JHTML::_('behavior.modal');
		
		ob_start();
		?>

<script type="text/javascript">

EasyBlog.ready(function($){

	window.hideSliders = function(active) {

		// Hide all elements from 0 - 4
		<?php for ( $i = 1; $i < 5; $i++) { ?>
			$('#<?php echo $i;?>-options').parent().hide();
		<?php } ?>

		// Hide latest options
		$('#latest-options').parent().hide();

		if (active == 'latest' || active == '0') {
			$('#latest-options').parent().show();
		} else {
			$('#' + active + '-options').parent().show();
		}
	};

	$('#moduletype-selection').bind('change' , function() {
		var current = $(this).val();

		hideSliders(current);
	});

	hideSliders( <?php echo $this->value;?> );
});

</script>

		<select name="<?php echo $this->name;?>" id="moduletype-selection">
			<option value="0"<?php echo $this->value == "0" ? ' selected="selected"' :'';?>><?php echo JText::_( 'Latest posts' ); ?></option>
			<option value="1"<?php echo $this->value == "1" ? ' selected="selected"' :'';?>><?php echo JText::_( 'Latest posts by blogger' );?></option>
			<option value="2"<?php echo $this->value == "2" ? ' selected="selected"' :'';?>><?php echo JText::_( 'Latest posts by category' );?></option>
			<option value="3"<?php echo $this->value == "3" ? ' selected="selected"' :'';?>><?php echo JText::_( 'Latest posts by tag' );?></option>
			<option value="4"<?php echo $this->value == "4" ? ' selected="selected"' :'';?>><?php echo JText::_( 'Latest posts by team blog' );?></option>
			<option value="5"<?php echo $this->value == "5" ? ' selected="selected"' :'';?>><?php echo JText::_( 'Latest posts by current active blogger' );?></option>
		</select>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
