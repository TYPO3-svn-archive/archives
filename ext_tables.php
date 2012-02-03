<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


$_extPath = t3lib_extMgm::extPath($_EXTKEY);
$_relPath = t3lib_extMgm::extRelPath($_EXTKEY);


// test
t3lib_div::loadTCA('pages');
$TCA['pages']['ctrl']['treeParentField'] = 'pid';
///

$TCA['tx_archives_documents'] = array(
	'ctrl' => array(
		'title'                    => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents',
		'label'                    => 'shelfmark',
		'label_alt'                => 'title',
		'label_alt_force'          => 1,
		'tstamp'                   => 'tstamp',
		'crdate'                   => 'crdate',
		'cruser_id'                => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'default_sortby'           => 'ORDER BY shelfmark',
		'delete'                   => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => $_extPath . 'tca.php',
		'iconfile'          => $_relPath . 'ext_icon/documents.png',
		'dividers2tabs'     => 1,
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, shelfmark, title, year, format, age, gender, material, technique, subject, genre',
	)
);

$TCA['tx_archives_material'] = array(
	'ctrl' => array(
		'title'           => 'LLL:EXT:archives/locallang_db.xml:tx_archives_material',
		'label'           => 'title',
	##	'label_alt'       => 'parent',
	##	'label_alt_force' => 1,
		'tstamp'          => 'tstamp',
		'crdate'          => 'crdate',
		'cruser_id'       => 'cruser_id',
		'default_sortby'  => 'ORDER BY parent, title',
		'delete'          => 'deleted',
		'enablecolumns'   => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => $_extPath . 'tca.php',
		'iconfile'          => $_relPath . 'ext_icon/category.png',
		'dividers2tabs'     => 1,
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, title, title_lang_ol, parent',
	)
);

$TCA['tx_archives_technique'] = $TCA['tx_archives_material'];
$TCA['tx_archives_technique']['ctrl']['title'] = 'LLL:EXT:archives/locallang_db.xml:tx_archives_technique';

$TCA['tx_archives_subject']   = $TCA['tx_archives_material'];
$TCA['tx_archives_subject']['ctrl']['title']   = 'LLL:EXT:archives/locallang_db.xml:tx_archives_subject';

$TCA['tx_archives_genre']     = $TCA['tx_archives_material'];
$TCA['tx_archives_genre']['ctrl']['title']     = 'LLL:EXT:archives/locallang_db.xml:tx_archives_genre';

$TCA['tx_archives_collection'] = array(
	'ctrl' => array(
		'title'          => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection',
		'label'          => 'title',
		'tstamp'         => 'tstamp',
		'crdate'         => 'crdate',
		'cruser_id'      => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete'         => 'deleted',
		'enablecolumns'  => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => $_extPath . 'tca.php',
		'iconfile'          => $_relPath . 'ext_icon/collection.png',
		'dividers2tabs'     => 1,
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, title, title_lang_ol, setup, provenance, particulars, comment, period, collector',
	)
);

$TCA['tx_archives_collector'] = array(
	'ctrl' => array(
		'title'           => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collector',
		'label'           => 'surname',
		'label_alt'       => 'firstname, location',
		'label_alt_force' => 1,
		'tstamp'          => 'tstamp',
		'crdate'          => 'crdate',
		'cruser_id'       => 'cruser_id',
		'default_sortby'  => 'ORDER BY surname',
		'delete'          => 'deleted',
		'enablecolumns'   => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => $_extPath . 'tca.php',
		'iconfile'          => $_relPath . 'ext_icon/collector.png',
		'dividers2tabs'     => 1,
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, surname, firstname, dateofbirth, career, education, teaching, employer, location, particulars',
	)
);
?>