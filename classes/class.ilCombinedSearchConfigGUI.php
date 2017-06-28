<?php
/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('Services/Component/classes/class.ilPluginConfigGUI.php');

/**
 * ContainerFilter configuration user interface class
 *
 * @author  Fabian Schmid <fabian.schmid@ilub.unibe.ch>
 * @version $Id$
 *
 */
class ilCombinedSearchConfigGUI extends ilPluginConfigGUI {
	/**
	 * @var array
	 */
	protected static $cache = array();
	/**
	 * @var array
	 */
	protected $checkboxes = array(
			'show_on_desktop',
			'show_centered_on_desktop',
			'show_in_repository',
	);
	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;


	/**
	 * Handles all commands, default is "configure"
	 */
	function performCommand($cmd) {
		switch ($cmd) {
			case 'configure':
			case 'save':
				$this->$cmd();
				break;
		}
	}


	/**
	 * Configure screen
	 */
	function configure() {
		global $DIC;

		$this->initConfigurationForm();
		$this->getValues();
		$DIC['tpl']->setContent($this->form->getHTML());
	}


	public function getValues() {
		$values = array();
		foreach ($this->checkboxes as $key => $cb) {
			if (! is_array($cb)) {
				$values[$cb] = $this->_getValue($cb);
			} else {
				$values[$key] = $this->_getValue($key);
				foreach ($cb as $field => $gui) {
					$values[$key . '_' . $field] = $this->_getValue($key . '_' . $field);
				}
			}
		}
		$this->form->setValuesByArray($values);
	}


	/**
	 * Init configuration form.
	 *
	 * @return object form object
	 */
	public function initConfigurationForm() {
		/** @var ilCtrl $ilCtrl */
		global $lng, $ilCtrl;
		require_once('Services/Form/classes/class.ilPropertyFormGUI.php');
		$this->form = new ilPropertyFormGUI();
		foreach ($this->checkboxes as $key => $cb) {
			if (! is_array($cb)) {
				$checkbox = new ilCheckboxInputGUI($this->getPluginObject()->txt($cb), $cb);
				$this->form->addItem($checkbox);
			} else {
				$checkbox = new ilCheckboxInputGUI($this->getPluginObject()->txt($key), $key);
				foreach ($cb as $field => $gui) {
					$sub = new $gui($this->getPluginObject()->txt($key . '_' . $field), $key . '_' . $field);
					$checkbox->addSubItem($sub);
				}
				$this->form->addItem($checkbox);
			}
		}
		$this->form->addCommandButton('save', $lng->txt('save'));
		$this->form->setTitle($this->getPluginObject()->txt('configuration'));
		$this->form->setFormAction($ilCtrl->getFormAction($this));

		return $this->form;
	}


	/**
	 * Save form input
	 */
	public function save() {
		/** @var ilCtrl $ilCtrl */
		global $tpl, $ilCtrl;
		$this->initConfigurationForm();
		if ($this->form->checkInput()) {
			// Save Checkbox Values
			foreach ($this->checkboxes as $key => $cb) {
				if (! is_array($cb)) {
					$this->setValue($cb, $this->form->getInput($cb));
				} else {
					$this->setValue($key, $this->form->getInput($key));
					foreach ($cb as $field => $gui) {
						$this->setValue($key . '_' . $field, $this->form->getInput($key . '_' . $field));
					}
				}
			}
			$ilCtrl->redirect($this, 'configure');
		} else {
			$this->form->setValuesByPost();
			$tpl->setContent($this->form->getHtml());
		}
	}


	/**
	 * @param string $key
	 * @param string $value
	 */
	public function setValue($key, $value) {
		/** @var ilDB $ilDB */
		global $ilDB;
		if (! is_string($this->_getValue($key))) {
			$ilDB->insert('ui_uihk_uihkcombsearch', array(
				'config_key' => array(
					'text',
					$key
				),
				'config_value' => array(
					'integer',
					$value
				)
			));
		} else {
			$ilDB->update('ui_uihk_uihkcombsearch', array(
				'config_key' => array(
					'text',
					$key
				),
				'config_value' => array(
					'integer',
					$value
				)
			), array(
				'config_key' => array(
					'text',
					$key
				)
			));
		}
	}


	/**
	 * @param string $key
	 *
	 * @return bool|string
	 */
	public static function _getValue($key) {
		if (! isset(self::$cache[$key])) {
			/** @var ilDB $ilDB */
			global $ilDB;
			$result = $ilDB->query('SELECT config_value FROM ui_uihk_uihkcombsearch WHERE config_key = '
				. $ilDB->quote($key, 'text'));
			if ($result->numRows() == 0) {
				return false;
			}
			$record = $ilDB->fetchAssoc($result);
			self::$cache[$key] = $record['config_value'];
		}

		return self::$cache[$key];
	}
}