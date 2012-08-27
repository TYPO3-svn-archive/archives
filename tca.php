<?php
/**
 * @ToDo:
 * configurable Storage location for
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}



$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['archives']);
switch ($extConf['store_records']) {
case 1:
	$pidStr = 'CURRENT_PID';
	break;
#case 3:
#	$pidStr = 'PAGE_TSCONFIG_ID';
#	break;
default:
	$pidStr = 'STORAGE_PID';
	break;
}

$confFields = array(
	'hidden' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
		'config'  => array(
			'type'    => 'check',
			'default' => '0'
		),
	),
	'title' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.title',
		'config' => array(
			'type' => 'input',
			'size' => '30',
			'eval' => 'required',
		),
	),
	'title_lang_ol' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_all.title_lang_ol',
		'config' => array(
			'type' => 'input',
			'size' => '30',
		),
	),
);

$confWizardsMaterial = array(
	'_PADDING' => 2,
	'_VERTICAL' => 1,
	'add' => array(
		'type' => 'script',
		'title' => 'Create new record',
		'icon' => 'add.gif',
		'params' => array(
			'table'=>'tx_archives_material',
			'pid' => '###' . $pidStr . '###',
			'setValue' => 'prepend'
		),
		'script' => 'wizard_add.php',
	),
/*
	'list' => array(
		'type' => 'script',
		'title' => 'List',
		'icon' => 'list.gif',
		'params' => array(
			'table'=>'tx_archives_material',
			'pid' => '###' . $pidStr . '###',
		),
		'script' => 'wizard_list.php',
	),
	'edit' => array(
		'type' => 'popup',
		'title' => 'Edit',
		'script' => 'wizard_edit.php',
		'popup_onlyOpenIfSelected' => 1,
		'icon' => 'edit2.gif',
		'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
	),
*/
);
$confWizardsTechnique                            = $confWizardsMaterial;
$confWizardsTechnique['add']['params']['table']  = 'tx_archives_technique';
$confWizardsTechnique['list']['params']['table'] = 'tx_archives_technique';
$confWizardsSubject                              = $confWizardsMaterial;
$confWizardsSubject['add']['params']['table']    = 'tx_archives_subject';
$confWizardsSubject['list']['params']['table']   = 'tx_archives_subject';
$confWizardsGenre                                = $confWizardsMaterial;
$confWizardsGenre['add']['params']['table']      = 'tx_archives_genre';
$confWizardsGenre['list']['params']['table']     = 'tx_archives_genre';
$confWizardsCollector                            = $confWizardsMaterial;
$confWizardsCollector['add']['params']['table']  = 'tx_archives_collector';
$confWizardsCollector['list']['params']['table'] = 'tx_archives_collector';



$TCA['tx_archives_material'] = array(
	'ctrl' => $TCA['tx_archives_material']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol,parent'
	),
	'feInterface' => $TCA['tx_archives_material']['feInterface'],
	'columns' => array(
		'hidden'        => $confFields['hidden'],
		'title'         => $confFields['title'],
		'title_lang_ol' => $confFields['title_lang_ol'],
		'parent'      => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_all.parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('',0),
				),
				'foreign_table' => 'tx_archives_material',
				'foreign_table_where' => 'AND tx_archives_material.pid=###' . $pidStr . '###
											AND tx_archives_material.parent=0
											ORDER BY tx_archives_material.title',
				'size'     => 20,
				'minitems' => 0,
				'maxitems' => 1,
				'wizards'  => $confWizardsMaterial,
				'treeView' => '1',  ##  test browser fe 3.9.7
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'title;;lll, parent,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					hidden;;1,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended'
		),
	),
	'palettes' => array(
		'lll' => array(
			'showitem' => 'title_lang_ol'
		),
	),
);



$_tempTCA                                    = $TCA['tx_archives_technique'];
$TCA['tx_archives_technique']                = $TCA['tx_archives_material'];
$TCA['tx_archives_technique']['ctrl']        = $_tempTCA['ctrl'];
$TCA['tx_archives_technique']['feInterface'] = $_tempTCA['feInterface'];
unset($_tempTCA);
$_tempTechniqueConfig                        =& $TCA['tx_archives_technique']['columns']['parent']['config'];
$_tempTechniqueConfig['foreign_table']       = 'tx_archives_technique';
$_tempTechniqueConfig['foreign_table_where'] = 'AND tx_archives_technique.pid=###' . $pidStr . '###
													AND tx_archives_technique.parent=0
													ORDER BY tx_archives_technique.title';
$_tempTechniqueConfig['wizards']             = $confWizardsTechnique;



$_tempTCA                                  = $TCA['tx_archives_subject'];
$TCA['tx_archives_subject']                = $TCA['tx_archives_material'];
$TCA['tx_archives_subject']['ctrl']        = $_tempTCA['ctrl'];
$TCA['tx_archives_subject']['feInterface'] = $_tempTCA['feInterface'];
unset($_tempTCA);
$_tempSubjectConfig                        =& $TCA['tx_archives_subject']['columns']['parent']['config'];
$_tempSubjectConfig['foreign_table']       = 'tx_archives_subject';
$_tempSubjectConfig['foreign_table_where'] = 'AND tx_archives_subject.pid=###' . $pidStr . '###
												AND tx_archives_subject.parent=0
												ORDER BY tx_archives_subject.title';
$_tempSubjectConfig['wizards']             = $confWizardsSubject;



$_tempTCA                                = $TCA['tx_archives_genre'];
$TCA['tx_archives_genre']                = $TCA['tx_archives_material'];
$TCA['tx_archives_genre']['ctrl']        = $_tempTCA['ctrl'];
$TCA['tx_archives_genre']['feInterface'] = $_tempTCA['feInterface'];
unset($_tempTCA);
$_tempGenreConfig                        =& $TCA['tx_archives_genre']['columns']['parent']['config'];
$_tempGenreConfig['foreign_table']       = 'tx_archives_genre';
$_tempGenreConfig['foreign_table_where'] = 'AND tx_archives_genre.pid=###' . $pidStr . '###
												AND tx_archives_genre.parent=0
												ORDER BY tx_archives_genre.title';
$_tempGenreConfig['wizards']             = $confWizardsGenre;



$TCA['tx_archives_collection'] = array(
	'ctrl' => $TCA['tx_archives_collection']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_lang_ol,
									setup,provenance,focus,comment,period,collector'
	),
	'feInterface' => $TCA['tx_archives_collection']['feInterface'],
	'columns' => array(
		'hidden'        => $confFields['hidden'],
		'title'         => $confFields['title'],
		'title_lang_ol' => $confFields['title_lang_ol'],
		'setup'         => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection.setup',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'date',
			),
		),
		'provenance' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection.provenance',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'focus' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection.focus',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			),
		),
		'comment' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_all.comment',
			'config' => array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '8',
			),
		),
		'period' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection.period',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'collector' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection.collector',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_archives_collector',
				'foreign_table_where' => 'AND tx_archives_collector.pid=###' . $pidStr . '###
											ORDER BY tx_archives_collector.surname',
				'size'                => 30,
				'minitems'            => 0,
				'maxitems'            => 20,
				'wizards'             => $confWizardsCollector,
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'title;;lll,
					setup, provenance,
					focus;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_archives/rte/],
					comment, period,
				--div--;LLL:EXT:archives/locallang_db.xml:tx_archives_collector,
					collector,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					hidden;;1,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended'
		),
	),
	'palettes' => array(
		'lll' => array('showitem' => 'title_lang_ol'),
	),
);



$TCA['tx_archives_collector'] = array(
	'ctrl' => $TCA['tx_archives_collector']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,surname,firstname,dateofbirth,career,location,comment'
	),
	'feInterface' => $TCA['tx_archives_collector']['feInterface'],
	'columns' => array(
		'hidden' => $confFields['hidden'],
		'surname' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.last_name',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'eval' => 'required',
			),
		),
		'firstname' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.first_name',
			'config' => array(
				'type' => 'input',
				'size' => '20',
			),
		),
		'dateofbirth' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collector.dateofbirth',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'eval' => 'date',
			),
		),
		'career' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collector.career',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			),
		),
		'location' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.city',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'comment' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_all.comment',
			'config' => array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '8',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => '--palette--;LLL:EXT:lang/locallang_general.xml:LGL.name;name;;1-1-1,
				dateofbirth,
				career;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_archives/rte/],
				location, comment,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					hidden;;1,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended'
		),
	),
	'palettes' => array(
		'name' => array(
			'showitem'       => 'surname, firstname',
			'canNotCollapse' => 1,
		),
	),
);



$TCA['tx_archives_documents'] = array(
	'ctrl' => $TCA['tx_archives_documents']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,shelfmark,title,year,format,age,gender,material,technique,subject,genre'
	),
	'feInterface' => $TCA['tx_archives_documents']['feInterface'],
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0),
				),
			),
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'foreign_table'       => 'tx_archives_documents',
				'foreign_table_where' => 'AND tx_archives_documents.pid=###' . $pidStr . '###
											AND tx_archives_documents.sys_language_uid IN (-1,0)',
			),
		),
		'l18n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			),
		),
		'hidden' => $confFields['hidden'],
		'shelfmark' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents.shelfmark',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			),
		),
		'title'  => $confFields['title'],
		'year' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents.year',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'year',
			),
		),
		'format' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents.format',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			),
		),
		'age' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents.age',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'range' => array('lower'=>0,'upper'=>120),
				'eval' => 'int',
			),
		),
		'gender' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents.gender',
			'config' => array(
				'type' => 'radio',
				'items' => array(
					array('LLL:EXT:archives/locallang_db.xml:tx_archives_documents.gender.I.0', '0'),
					array('LLL:EXT:archives/locallang_db.xml:tx_archives_documents.gender.I.1', '1'),
				),
			),
		),
		'collection' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_collection',
			'config' => array(
				'type'                => 'select',
				'items'               => array(
					array('',0),
				),
				'foreign_table'       => 'tx_archives_collection',
				'foreign_table_where' => 'AND tx_archives_collection.pid=###' . $pidStr . '###
											ORDER BY tx_archives_collection.title',
				'size'                => 1,
				'minitems'            => 0,
				'maxitems'            => 1,
			),
		),
		'material' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_material',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_archives_material',
				'foreign_table_where' => 'AND tx_archives_material.pid=###' . $pidStr . '###
											ORDER BY tx_archives_material.title',
				'size'                => 20,
				'minitems'            => 0,
				'maxitems'            => 10,
				'wizards'             => $confWizardsMaterial,
				'renderMode'          => 'tree',
				'treeConfig'          => array(
					'parentField' => 'parent',
					'appearance'  => array(
					##	'expandAll'  => true,
						'showHeader' => true,
					),
				),
			),
		),
		'technique' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_technique',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_archives_technique',
				'foreign_table_where' => 'AND tx_archives_technique.pid=###' . $pidStr . '###
											ORDER BY tx_archives_technique.title',
				'size'                => 20,
				'minitems'            => 0,
				'maxitems'            => 10,
				'wizards'             => $confWizardsTechnique,
				'renderMode'          => 'tree',
				'treeConfig'          => array(
					'parentField' => 'parent',
					'appearance'  => array(
					##	'expandAll'  => true,
						'showHeader' => true,
					),
				),
			),
		),
		'subject' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_subject',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_archives_subject',
				'foreign_table_where' => 'AND tx_archives_subject.pid=###' . $pidStr . '###
											ORDER BY tx_archives_subject.title',
				'size'                => 20,
				'minitems'            => 0,
				'maxitems'            => 10,
				'wizards'             => $confWizardsSubject,
				'renderMode'          => 'tree',
				'treeConfig'          => array(
					'parentField' => 'parent',
					'appearance'  => array(
					##	'expandAll'  => true,
						'showHeader' => true,
					),
				),
			),
		),
		'genre' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:archives/locallang_db.xml:tx_archives_genre',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'tx_archives_genre',
				'foreign_table_where' => 'AND tx_archives_genre.pid=###' . $pidStr . '###
											ORDER BY tx_archives_genre.title',
				'size'                => 20,
				'minitems'            => 0,
				'maxitems'            => 10,
				'wizards'             => $confWizardsGenre,
				'renderMode'          => 'tree',
				'treeConfig'          => array(
					'parentField' => 'parent',
					'appearance'  => array(
					##	'expandAll'  => true,
						'showHeader' => true,
					),
				),
			),
		),
		'image'                 => $TCA['tt_content']['columns']['image'],
		'imagewidth'            => $TCA['tt_content']['columns']['imagewidth'],
		'imageheight'           => $TCA['tt_content']['columns']['imageheight'],
		'imageorient'           => $TCA['tt_content']['columns']['imageorient'],
		'imagecaption'          => $TCA['tt_content']['columns']['imagecaption'],
		'imagecaption_position' => $TCA['tt_content']['columns']['imagecaption_position'],
		'imagecols'             => $TCA['tt_content']['columns']['imagecols'],
		'imageborder'           => $TCA['tt_content']['columns']['imageborder'],
		'image_link'            => $TCA['tt_content']['columns']['image_link'],
		'image_zoom'            => $TCA['tt_content']['columns']['image_zoom'],
		'image_noRows'          => $TCA['tt_content']['columns']['image_noRows'],
		'image_effects'         => $TCA['tt_content']['columns']['image_effects'],
		'image_compression'     => $TCA['tt_content']['columns']['image_compression'],
		'image_frames'          => $TCA['tt_content']['columns']['image_frames'],
		'imageseo' => array(
			'exclude'   => 1,
			'l10n_mode' => 'prefixLangTitle',
			'label'     => 'LLL:EXT:archives/locallang_db.xml:tx_archives_documents.imageseo',
			'config'    => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			),
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource,
					shelfmark, title;;;;2-2-2, year;;;;3-3-3, format,
					--palette--;LLL:EXT:lang/locallang_general.xml:LGL.author;author;;1-1-1,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.images,
					--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.imagefiles;imagefiles;;1-1-1,
					--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.imagelinks;imagelinks,
					--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.image_accessibility;image_accessibility,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
					--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.image_settings;image_settings,
					--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.imageblock;imageblock,
				--div--;LLL:EXT:lang/locallang_common.xml:category,
					collection,
					--palette--;LLL:EXT:archives/locallang_db.xml:tx_archives_documents.material_technique;material_technique,
					--palette--;LLL:EXT:archives/locallang_db.xml:tx_archives_documents.subject_genre;subject_genre,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					hidden;;1,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended',
		),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'author' => array(
			'showitem' => 'age, gender',
			'canNotCollapse' => 1,
		),
		'imagefiles'          => $TCA['tt_content']['palettes']['imagefiles'],
		'imagelinks'          => $TCA['tt_content']['palettes']['imagelinks'],
		'image_accessibility' => array(
			'showitem'       => 'imageseo',
			'canNotCollapse' => 1,
		),
		'image_settings'      => $TCA['tt_content']['palettes']['image_settings'],
		'imageblock'          => $TCA['tt_content']['palettes']['imageblock'],
		'material_technique' => array(
			'showitem'       => 'material, technique',
			'canNotCollapse' => 1,
		),
		'subject_genre' => array(
			'showitem'       => 'subject, genre',
			'canNotCollapse' => 1,
		),
	),
);
$TCA['tx_archives_documents']['columns']['image']['config']['uploadfolder'] = 'uploads/tx_archives';




/**
 * Setting up TCA_DESCR - Context Sensitive Help
 */
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_material',   'EXT:archives/csh/locallang_csh_material.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_technique',  'EXT:archives/csh/locallang_csh_technique.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_subject',    'EXT:archives/csh/locallang_csh_subject.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_genre',      'EXT:archives/csh/locallang_csh_genre.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_collection', 'EXT:archives/csh/locallang_csh_collection.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_collector',  'EXT:archives/csh/locallang_csh_collector.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_archives_documents',  'EXT:archives/csh/locallang_csh_documents.xml');
?>