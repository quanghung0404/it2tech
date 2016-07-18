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



$typefields = array('corefield', 'billto');
$corefields = VirtueMartModelUserfields::getCoreFields();
foreach ($typefields as $typefield) {
    $_k = 0;
    $_set = false;
    $_table = false;
    $_hiddenFields = '';

//             for ($_i = 0, $_n = count($this->userFields['fields']); $_i < $_n; $_i++) {
    for ($_i = 0, $_n = count($this->userFields['fields']); $_i < $_n; $_i++) {
	// Do this at the start of the loop, since we're using 'continue' below!
	if ($_i == 0) {
	    $_field = current($this->userFields['fields']);
	} else {
	    $_field = next($this->userFields['fields']);
	}

	if ($_field['hidden'] == true) {
	    $_hiddenFields .= $_field['formcode'] . "\n";
	    continue;
	}
	if ($_field['type'] == 'delimiter') {
	    if ($_set) {
		// We're in Fieldset. Close this one and start a new
		if ($_table) {
		    echo '	</table>' . "\n";
		    $_table = false;
		}
		echo '</fieldset>' . "\n";
	    }
	    $_set = true;
	    echo '<fieldset>' . "\n";
	    echo '	<legend>' . "\n";
	    echo '		' . $_field['title'];
	    echo '	</legend>' . "\n";
	    continue;
	}



	if (($typefield == 'corefield' && (in_array($_field['name'], $corefields) && $_field['name'] != 'email' && $_field['name'] != 'agreed') )
		or ($typefield == 'billto' && !(in_array($_field['name'], $corefields) && $_field['name'] != 'email' && $_field['name'] != 'agreed') )) {
	    if (!$_table) {
		// A table hasn't been opened as well. We need one here,
		if ( $typefield == 'corefield') {
		    echo '<span class="userfields_info">' . $this->corefield_title . '</span>';
		} else {
		    echo '<span class="userfields_info">' . $this->vmfield_title . '</span>';
		}


		echo '	<table  class="adminForm user-details">' . "\n";
		$_table = true;
	    }
	    echo '		<tr>' . "\n";
	    echo '			<td class="key" title="'.$_field['description'].'" >' . "\n";
	    echo '				<label class="' . $_field['name'] . '" for="' . $_field['name'] . '_field">' . "\n";
	    echo '					' . $_field['title'] . ($_field['required'] ? ' *' : '') . "\n";
	    echo '				</label>' . "\n";
	    echo '			</td>' . "\n";
	    echo '			<td>' . "\n";
	    echo '				' . $_field['formcode'] . "\n";
	    echo '			</td>' . "\n";
	    echo '		</tr>' . "\n";
	}
    }

    if ($_table) {
	echo '	</table>' . "\n";
    }
    if ($_set) {
	echo '</fieldset>' . "\n";
    }
    $_k = 0;
    $_set = false;
    $_table = false;
    $_hiddenFields = '';
    if(is_array($this->userFields['fields'])) {
		reset($this->userFields['fields']);
    }

}

echo $_hiddenFields;

