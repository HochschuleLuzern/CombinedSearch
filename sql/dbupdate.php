<?php
/**
 * Copyright (c) 2017 Hochschule Luzern
 *
 * This file is part of the NotifyOnCronFailure-Plugin for ILIAS.

 * NotifyOnCronFailure-Plugin for ILIAS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * NotifyOnCronFailure-Plugin for ILIAS is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventoImport-Plugin for ILIAS.  If not,
 * see <http://www.gnu.org/licenses/>.
 */
?>
<#1>
<?php
/**
 * @var ilDB $db
 */
$db = $ilDB;
if (!$db->tableExists('ui_uihk_uihkcombsearch')) {
	$fields = array(
		'config_key' => array(
			'type' => 'text',
			'length' => 64,
		),
		'config_value' => array(
			'type' => 'integer',
			'length' => 1,
		)
	);

	$db->createTable('ui_uihk_uihkcombsearch', $fields);
	$db->addPrimaryKey('ui_uihk_uihkcombsearch', array('config_key'));
	$stmt = $db->prepare('INSERT INTO ' . 'ui_uihk_uihkcombsearch'.
		' (config_key, config_value) VALUES (?, ?)', array('text', 'integer'));
	$db->execute($stmt, array('show_on_desktop', 1));
	$db->execute($stmt, array('show_centered_on_desktop', 1));
	$db->execute($stmt, array('show_in_repository', 0));
}
?>