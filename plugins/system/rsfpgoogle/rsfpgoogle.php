<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemRSFPGoogle extends JPlugin
{
	
	public function __construct(&$subject, $config) {
		parent::__construct( $subject, $config );
	}
	
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs) {
		
		JFactory::getLanguage()->load('plg_system_rsfpgoogle');
	
		$tabs->addTitle(JText::_('RSFP_GOOGLE_LABEL'), 'form-google');
		$tabs->addContent($this->googleConfigurationScreen());
	}
	
	public function rsfp_f_onBeforeFormDisplay($args) {
		$code = RSFormProHelper::getConfig('google.code');
		if (empty($code))
			return;
		
		$script = '<script type="text/javascript">'."\n";
		$script .= "\t".'var _gaq = _gaq || [];'."\n";
		$script .= "\t".'_gaq.push([\'_setAccount\', \''.$code.'\']);'."\n";
		$script .= "\t".'_gaq.push([\'_trackPageview\']);'."\n";
		$script .= "\t".'(function() {'."\n";
		$script .= "\t\t".'var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;'."\n";
		$script .= "\t\t".'ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';'."\n";
		$script .= "\t\t".'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);'."\n";
		$script .= "\t".'})();'."\n";
		$script .= '</script>'."\n";
		
		$doc = JFactory::getDocument();
		if ($doc->getType() == 'html') {
			RSFormProAssets::addCustomTag($script);
		}
	}
	
	public function rsfp_f_onAfterShowThankyouMessage($args) {
		$code = RSFormProHelper::getConfig('google.code');
		if (empty($code))
			return;
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT FormName FROM #__rsform_forms WHERE FormId = '".(int) $args['formId']."'");
		$formName = $db->loadResult();
		
		$script = '<script type="text/javascript">'."\n";
		$script .= "\t".'var _gaq = _gaq || [];'."\n";
		$script .= "\t".'_gaq.push([\'_setAccount\', \''.$code.'\']);'."\n";
		$script .= "\t".'_gaq.push([\'_trackPageview\',\''.addslashes(RSFormProHelper::htmlEscape($formName)).'\']);'."\n";
		$script .= "\t".'(function() {'."\n";
		$script .= "\t\t".'var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;'."\n";
		$script .= "\t\t".'ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';'."\n";
		$script .= "\t\t".'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);'."\n";
		$script .= "\t".'})();'."\n";
		$script .= '</script>'."\n";
		
		$doc = JFactory::getDocument();
		if ($doc->getType() == 'html') {
			RSFormProAssets::addCustomTag($script);
		}
	}
	
	public function googleConfigurationScreen() {
		ob_start();
		$code = RSFormProHelper::getConfig('google.code'); ?>
		<div id="page-google">
		<table class="admintable">
			<tr>
				<td width="200" style="width: 200px;" align="right" class="key"><label for="code"><?php echo JText::_('RSFP_GOOGLE_CODE'); ?></label></td>
				<td><input type="text" size="100" name="rsformConfig[google.code]" value="<?php echo RSFormProHelper::htmlEscape($code); ?>" /></td>
			</tr>
		</table>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}