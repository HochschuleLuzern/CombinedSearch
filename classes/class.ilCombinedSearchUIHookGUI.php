<?php
/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('Services/UIComponent/classes/class.ilUIHookPluginGUI.php');
require_once('class.ilCombinedSearchConfigGUI.php');

/**
 * ilCombinedSearchUIHookGUI class for Personal Desktop jQuery Search Block
 * @author			  Stephan Winiker <stephan.winiker@hslu.ch>
 * @version           $Id$
 */
class ilCombinedSearchUIHookGUI extends ilUIHookPluginGUI {

	const PERSONAL_DESKTOP_SELECTOR = '#block_pditems_0';
	const REPOSITORY_SELECTOR = '.ilCombinedSearch';


	/**
	 * Get html for a user interface area
	 *
	 * @param string $a_comp
	 * @param string $a_part
	 * @param array  $a_par
	 *
	 * @return array
	 */
	function getHTML($a_comp, $a_part, $a_par = array()) {
		if (ilCombinedSearchConfigGUI::_getValue('show_on_desktop')) {
			if (ilCombinedSearchConfigGUI::_getValue('show_centered_on_desktop')) {
				$place = 'center_column';
			} else {
				$place = 'right_column';
			}
			
			//if ($a_comp == 'Services/PersonalDesktop' && $a_part == $place) {
            if ($a_comp == 'Services/Dashboard' && $a_part == $place) {
				return array('mode' => ilUIHookPluginGUI::PREPEND,
						'html' => $this->getBlockHTML(self::PERSONAL_DESKTOP_SELECTOR));
			}
		}

		if (ilCombinedSearchConfigGUI::_getValue('show_in_repository')) {
			if ($a_comp == 'Services/Container' && $a_part == 'right_column') {
				return array('mode' => ilUIHookPluginGUI::PREPEND,
						'html' => $this->getBlockHTML(self::REPOSITORY_SELECTOR));
			}
		}
		// in all other cases, keep everything as it is
		return array('mode' => ilUIHookPluginGUI::KEEP, 'html' => '');
	}


	/**
	 * @param string $content_selector jQuery selector
	 *
	 * @return string
	 */
	function getBlockHTML($content_selector) {
		include_once('class.ilCombinedSearchBlockGUI.php');
		$block = new ilCombinedSearchBlockGUI($content_selector);

		return $block->getHTML();
	}
}
