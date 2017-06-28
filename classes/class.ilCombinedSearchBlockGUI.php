<?php
/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('Services/Block/classes/class.ilBlockGUI.php');
require_once('class.ilCombinedSearchPlugin.php');

/**
 * BlockGUI class for Content Filter jQuery Search Block
 * @author            Fabian Schmid <fabian.schmid@ilub.unibe.ch>
 * @version           $Id$
 * @ilCtrl_IsCalledBy ilCombinedSearchBlockGUI: ilColumnGUI
 */
class ilCombinedSearchBlockGUI extends ilBlockGUI {
	static $block_type = 'containerfilter';
	protected $container_selector;

	/**
	 * Constructor
	 */
	public function __construct($container_selector) {
		parent::__construct();
		$this->container_selector = $container_selector;
		$this->plugin = new ilCombinedSearchPlugin();
		$this->setTitle($this->plugin->txt('title'));
	}

	/**
	 * Get block type
	 * @return    string    Block type.
	 */
	static function getBlockType() {
		return self::$block_type;
	}

	/**
	 * Get block type
	 * @return    string    Block type.
	 */
	static function isRepositoryObject() {
		return false;
	}

	/**
	 * Fill data section
	 */
	public function fillDataSection() {
		global $DIC;
		include_once 'Services/Search/classes/class.ilSearchSettings.php';
		$search = $this->plugin->getTemplate('tpl.jquerysearch.html', true, true);
		if($DIC['rbacsystem']->checkAccess('search',ilSearchSettings::_getSearchSettingRefId()))
		{
			$search->setVariable('FORMACTION','ilias.php?baseClass=ilSearchController&cmd=post'.
					'&rtoken='.$DIC['ilCtrl']->getRequestToken().'&fallbackCmd=remoteSearch');
		} else {
			//Need to deactivate global search
		}
		$search->setVariable('ENTER_SEARCHSTRING', $this->plugin->txt('enter_searchstring'));
		$search->setVariable('LIST', $this->getContainerSelector());
		$search->setVariable('SEARCH_IN_TREE', $this->plugin->txt('search_in_tree'));
		$this->setDataSection($search->get());
	}


	/**
	 * @param string $container_selector
	 */
	public function setContainerSelector($container_selector) {
		$this->container_selector = $container_selector;
	}


	/**
	 * @return string
	 */
	public function getContainerSelector() {
		return $this->container_selector;
	}
}