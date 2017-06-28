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