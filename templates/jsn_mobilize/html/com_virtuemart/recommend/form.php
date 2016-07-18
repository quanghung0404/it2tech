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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$min = VmConfig::get('asks_minimum_comment_length', 50);
$max = VmConfig::get('asks_maximum_comment_length', 2000);
vmJsApi::JvalideForm();
$document = JFactory::getDocument();
$document->addScriptDeclaration('
	jQuery(function($){
			$("#askform").validationEngine("attach");
			$("#comment").keyup( function () {
				var result = $(this).val();
					$("#counter").val( result.length );
			});
	});
');
/* Let's see if we found the product */
if (empty ( $this->product )) {
	echo JText::_ ( 'COM_VIRTUEMART_PRODUCT_NOT_FOUND' );
	echo '<br /><br />  ' . $this->continue_link_html;
} else { ?>

<div class="ask-a-question-view">
	<h1><?php echo JText::_('COM_VIRTUEMART_PRODUCT_RECOMMEND')  ?></h1>

	<div class="product-summary">
		<div class="width70 floatleft">
			<h2><?php echo $this->product->product_name ?></h2>

			<?php // Product Short Description
			if (!empty($this->product->product_s_desc)) { ?>
				<div class="short-description">
					<?php echo $this->product->product_s_desc ?>
				</div>
			<?php } // Product Short Description END ?>

		</div>

		<div class="width30 floatleft center">
			<?php // Product Image
			echo $this->product->images[0]->displayMediaThumb('class="modal product-image"',false); ?>
		</div>

	<div class="clear"></div>
	</div>

	<div class="form-field">

		<form method="post" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$this->product->virtuemart_product_id.'&virtuemart_category_id='.$this->product->virtuemart_category_id.'&tmpl=component') ; ?>" name="askform" id="askform" >
			<label><?php echo JText::_('COM_VIRTUEMART_USER_FORM_EMAIL')  ?> : <input type="text" value="" name="email" id="email" size="30" class="validate[required,custom[email]]"/></label>
				<br />
			<label>
				<?php
				$ask_comment = JText::sprintf('COM_VIRTUEMART_COMMENT', $min, $max);
				echo $ask_comment;
				?>
				<br />
				<textarea title="<?php echo $ask_comment ?>" class="validate[required,minSize[<?php echo $min ?>],maxSize[<?php echo $max ?>]] field" id="comment" name="comment" rows="10"></textarea>
			</label>

				<div class="submit">
					<input class="highlight-button" type="submit" name="submit_ask" title="<?php echo JText::_('COM_VIRTUEMART_RECOMMEND_SUBMIT')  ?>" value="<?php echo JText::_('COM_VIRTUEMART_RECOMMEND_SUBMIT')  ?>" />

					<div class="width50 floatright right paddingtop">
						<?php echo JText::_('COM_VIRTUEMART_ASK_COUNT')  ?>
						<input type="text" value="0" size="4" class="counter" id="counter" name="counter" maxlength="4" readonly="readonly" />
					</div>
				</div>

				<input type="hidden" name="virtuemart_product_id" value="<?php echo JRequest::getInt('virtuemart_product_id',0); ?>" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="view" value="productdetails" />
				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="hidden" name="virtuemart_category_id" value="<?php echo JRequest::getInt('virtuemart_category_id'); ?>" />
				<input type="hidden" name="task" value="mailRecommend" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>

	</div>
</div>

<?php } ?>
