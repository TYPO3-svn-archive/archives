<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Ulfried Herrmann <herrmann@die-netzmacher.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once PATH_tslib.'class.tslib_pibase.php';


/**
 * Plugin 'archives' for the 'archives' extension.
 *
 * @author    Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package    TYPO3
 * @subpackage    tx_archives
 */
class tx_archives_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_archives_pi1';                   // Same as class name
	public $scriptRelPath    = 'pi1/class.tx_archives_pi1.php';  // Path to this script relative to the extension dir.
	public $extKey           = 'archives';                       // The extension key.
	public $extKeyTx         = 'tx_archives';                    // The extension key.
	public $pi_checkCHash    = TRUE;

	protected $template      = array();
	protected $filtersActive = FALSE;
	protected $filterSetting = array(
		'andWhere'      => '',
		'activeFilters' => array(),
	);

	/**
	 * The main method of the PlugIn
	 *
	 * @param    string        $content: The PlugIn content
	 * @param    array        $conf: The PlugIn configuration
	 * @return    The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$timeTrack = t3lib_div::makeInstance('t3lib_timeTrack');
		$GLOBALS['TT'] = new $timeTrack();
		$GLOBALS['TT']->start();

		$this->initConfig($conf);

		$content .= (empty ($this->piVars['showUid'])) ? $this->listView($content) : $this->singleView($content);

		$content .= '<div style="visibility: hidden;"><i>' . ($GLOBALS['TT']->getDifferenceToStarttime() / 1000) . ' s</i></div>';
		return $this->pi_wrapInBaseClass($content);
	}


	/**
	 *
	 */
	protected function initConfig($conf) {
		$this->conf = $conf;
		$this->conf['pidList']   = $this->cObj->data['pages'];
		$this->conf['recursive'] = $this->cObj->data['recursive'];

		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$this->internal['currentTable']       = $this->extKeyTx . '_documents';
		$this->internal['searchFieldList']    = ($this->conf['listView.']['internal.']['searchFieldList']) ?
													$this->conf['listView.']['internal.']['searchFieldList'] : 'shelfmark,title';
		$this->internal['orderBy']            = ($this->conf['listView.']['internal.']['orderBy']) ?
													$this->conf['listView.']['internal.']['orderBy'] : '';
		$this->internal['orderByList']        = ($this->conf['listView.']['internal.']['orderByList']) ?
													implode(',', t3lib_div::trimExplode(',', $this->conf['listView.']['internal.']['orderByList'])) : '';
		$this->internal['dontLinkActivePage'] = ($this->conf['listView.']['internal.']['dontLinkActivePage']) ? TRUE : FALSE;
		$this->internal['maxPages']           = ($this->conf['listView.']['internal.']['maxPages']) ?
													(int)$this->conf['listView.']['internal.']['maxPages'] : 8;
		$this->internal['pagefloat']          = ($this->conf['listView.']['internal.']['pagefloat']) ?
													$this->conf['listView.']['internal.']['pagefloat'] : 'center';
		$this->internal['showFirstLast']      = ($this->conf['listView.']['internal.']['showFirstLast']) ? TRUE : FALSE;
		$this->internal['showRange']          = ($this->conf['listView.']['internal.']['showRange']) ? TRUE : FALSE;
		$this->pi_listFields               = ($this->conf['listView.']['listFields']) ? $this->conf['listView.']['listFields'] : '*';

		$this->loadTemplate();
	}


	/**
	 *
	 */
	protected function loadTemplate() {
		$this->template['all'] = $this->cObj->fileResource($this->conf['template.']['file']);
	}


	/**
	 *
	 */
	protected function listView($content) {
		$this->template['list'] = $this->cObj->getSubpart($this->template['all'], 'TEMPLATE_LIST');

		$this
			->listen4Filters()
			->pi_list_searchBox()
			->list_categoryMenu();

		if (!empty ($this->conf['listView.']['emptyListAtStart']) AND empty ($this->piVars['sword']) AND empty ($this->filtersActive)) {
		} else {
				//  get item list
			$addWhere = $this->filterSetting['andWhere'];
			$res = $this->pi_exec_query($this->extKeyTx . '_documents', $count = 0, $addWhere, $mm_cat = '', $groupBy = '', $orderBy = '', $query = '');
			$this->pi_list_makelist($res);

				//  count items
			$res = $this->pi_exec_query($this->extKeyTx . '_documents', $count = 1, $addWhere);
			$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$this->internal['res_count']          = $ftc['count(*)'];
			$browseresults = $this->pi_list_browseresults($showResultCount=1,$tableParams='',$wrapArr = array(), $pointerName = 'pointer', $hscText = FALSE);
			$this->template['list']      = $this->cObj->substituteSubpart($this->template['list'], 'BROWSER_RESULTS', $browseresults);
			$content .= $this->template['list'];
		}

		return $content;
	}

	/**
	 * Listens for filter piVars and get related item uids
	 *
	 * @param   array       category items
	 * @return	obj   		this
	 */
	public function listen4Filters() {
			//  prepare query
		$select_fields = 'DISTINCT d.uid';
		$from_table    = 'tx_archives_documents d';
		$where_clause  = '1';

		foreach ($this->conf['listView.']['categories.'] as $category) {
			if (!empty ($this->piVars[$category]) AND is_array($this->piVars[$category])) {
					//  set filtering active
				$this->filtersActive = TRUE;
					//  store current filters for reuse in form ('selected')
				$this->filterSetting['activeFilters'][$category] = $GLOBALS['TYPO3_DB']->cleanIntArray($this->piVars[$category]);
					//  add andWhere for each category filter
				$from_table         .= LF . 'LEFT JOIN tx_archives_documents_mm_tx_archives_' . $category . ' mm_' . $category . '
										ON d.uid = mm_' . $category . '.uid_local
										LEFT JOIN tx_archives_' . $category . ' c_' . $category . '
										ON mm_' . $category . '.uid_foreign = c_' . $category . '.uid';
				$where_clause       .= LF . 'AND c_' . $category . '.uid IN (' . implode(',', $this->filterSetting['activeFilters'][$category]) . ')';
			}
		}

			//  get items by used categories
		if ($this->filtersActive === TRUE) {
			$groupBy       = '';
			$orderBy       = '';
			$limit         = '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$uids = array();
			while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$uids[] = $ftc['uid'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
				//  pass andWhre to main query
			$this->filterSetting['andWhere'] = 'AND uid IN (' . implode(',', $GLOBALS['TYPO3_DB']->cleanIntArray($uids)) . ')';
		}

		return $this;
	}

	/**
	 * Returns a Search box, sending search words to piVars "sword" and setting the "no_cache" parameter as well in the form.
	 * Submits the search request to the current REQUEST_URI
	 *
	 * @return	obj   		this
	 * @see tslib_pibase::pi_list_searchBox()
	 */
	public function pi_list_searchBox() {
		$this->template['searchBox'] = $this->cObj->getSubpart($this->template['all'], 'SEARCHBOX');
		$markerArray = array(
			'###ACTION###'       => htmlspecialchars(t3lib_div::getIndpEnv('REQUEST_URI')),
		);
		$this->template['list']  = $this->cObj->substituteMarkerArray($this->template['list'], $markerArray);
		$markerArray = array(
			'###INPUT_NAME###'   => $this->prefixId.'[sword]',
			'###INPUT_VALUE###'  => htmlspecialchars($this->piVars['sword']),
			'###SUBMIT_VALUE###' => $this->pi_getLL('pi_list_searchBox_search', 'Search', TRUE),
			'###POINTER_NAME###' => $this->prefixId.'[pointer]',
		);
		$this->template['searchBox'] = $this->cObj->substituteMarkerArray($this->template['searchBox'], $markerArray);
		$this->template['list']      = $this->cObj->substituteSubpart($this->template['list'], 'SEARCHBOX', $this->template['searchBox']);

		return $this;
	}

	/**
	 * Gets the category_menu
	 *
	 * @return	obj   		this
	 */
	public function list_categoryMenu() {
		$content = '';
		$this->template['categoryMenu']         = $this->cObj->getSubpart($this->template['all'], 'CATEGORY_MENU');
		$this->template['categoryMenuOptgroup'] = $this->cObj->getSubpart($this->template['categoryMenu'], 'OPTGROUP_TPL');
		$this->template['categoryMenuOption']   = $this->cObj->getSubpart($this->template['categoryMenu'], 'OPTION_TPL');

		foreach ($this->conf['listView.']['categories.'] as $category) {
				//  get used categories from mm table
			$select_fields = 'DISTINCT uid_foreign';
			$where_clause  = 'uid_foreign > 0';
			$groupBy       = '';
			$orderBy       = '';
			$limit         = '';
			$from_table    = 'tx_archives_documents_mm_tx_archives_' . $category;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$categoriesUsed = array();
			while($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$categoriesUsed[] = $ftc['uid_foreign'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			$categoriesUsed = implode(',', $GLOBALS['TYPO3_DB']->cleanIntArray($categoriesUsed));

				//  get categories title
			$select_fields = 'uid, title, parent';
			$where_clause  = 'pid IN (' . $this->pi_getPidList($this->conf['pidList'], $this->conf['recursive']) . ')
								' . $this->cObj->enableFields('tx_archives_' . $category) . '
								AND uid IN (' . $categoriesUsed . ')';
			$groupBy       = '';
			$orderBy       = 'parent ASC, title ASC';
			$limit         = '';
			$from_table    = 'tx_archives_' . $category;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
			$categories = array();
			while($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$categories[$ftc['parent']][$ftc['uid']] = $ftc['title'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);

			$content .= $this->list_categoryMenuRender($category, $categories);
		}

		$this->template['list'] = $this->cObj->substituteSubpart($this->template['list'], 'CATEGORY_MENU', $content);

		return $this;
	}

	/**
	 * Renders the category_menu
	 *
	 * @param   array       category items
	 * @return	obj   		this
	 */
	public function list_categoryMenuRender($category, $categories) {
		$contentOptGroups = '';

			//  start with parent categories
		foreach ($categories[0] as $cUid => $cTitle) {
			$contentOptGroup = '';
			if (!array_key_exists($cUid, $categories)) {
					//  option w/o children
				$markerArray = array(
					'###OPTION_CLASS###'    => 'class="parent-wo-children"',
					'###OPTION_VALUE###'    => (int)$cUid,
					'###OPTION_LABEL###'    => $cTitle,
					'###OPTION_SELECTED###' => (in_array($cUid, $this->filterSetting['activeFilters'][$category])) ? 'selected="selected"' : '',
				);
				$contentOptGroup .= $this->cObj->substituteMarkerArray($this->template['categoryMenuOption'], $markerArray);
			} else {
					//  optgroup + children
				$markerArray = array(
					'###OPTGROUP_LABEL###'  => $cTitle,
					'###OPTION_CLASS###'    => 'class="parent"',
					'###OPTION_VALUE###'    => (int)$cUid,
					'###OPTION_LABEL###'    => $this->pi_getLL('label_allEntries', 'All Entries'),
					'###OPTION_SELECTED###' => (in_array($cUid, $this->filterSetting['activeFilters'][$category])) ? 'selected="selected"' : '',
				);
				$contentOptGroup .= $this->cObj->substituteMarkerArray($this->template['categoryMenuOptgroup'], $markerArray);

					//  children
				$contentOption  = '';
				$contentOption .= $this->cObj->substituteMarkerArray($this->template['categoryMenuOption'], $markerArray);
				foreach ($categories[$cUid] as $ccUid => $ccTitle) {
					$markerArray = array(
						'###OPTION_CLASS###'    => '',
						'###OPTION_VALUE###'    => (int)$ccUid,
						'###OPTION_LABEL###'    => $ccTitle,
						'###OPTION_SELECTED###' => (in_array($ccUid, $this->filterSetting['activeFilters'][$category])) ? 'selected="selected"' : '',
					);
					$contentOption .= $this->cObj->substituteMarkerArray($this->template['categoryMenuOption'], $markerArray);
				}
				$contentOptGroup = $this->cObj->substituteSubpart($contentOptGroup, 'OPTION_TPL', $contentOption);
			}

			$contentOptGroups .= $contentOptGroup;
		}

		$markerArray = array(
			'###TABLE###' => $category,
			'###SELECTBOX_LABEL###' => $this->pi_getLL('tx_archives_' . $category),
			'###PREFIXID###' => $this->prefixId,
			'###SIZE###' => (int)$this->conf['listView.']['categoryMenu.']['select.']['size'],
		);
		$templateCategoryMenu = $this->cObj->substituteMarkerArray($this->template['categoryMenu'], $markerArray);
		$templateCategoryMenu = $this->cObj->substituteSubpart($templateCategoryMenu, 'OPTGROUP_TPL', $contentOptGroups);

		return $templateCategoryMenu;
	}

	/**
	 * Returns the list of items based on the input SQL result pointer
	 * For each result row the internal var, $this->internal['currentRow'], is set with the row returned.
	 * $this->pi_list_header() makes the header row for the list
	 * $this->pi_list_row() is used for rendering each row
	 * Notice that these two functions are typically ALWAYS defined in the extension class of the plugin since they are directly concerned with the specific layout for that plugins purpose.
	 *
	 * @param	pointer		Result pointer to a SQL result which can be traversed.
	 * @return	obj   		this
	 * @see pi_list_row(), pi_list_header()
	 * @see tslib_pibase::pi_list_makelist()
	 */
	public function pi_list_makelist($res) {
			// Make list table header:
		$this->pi_list_header();

			// Make list table rows
		$tRows                        = array();
		$this->internal['currentRow'] = '';
		$c = 0;
		while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$tRows[] = $this->pi_list_row($c);
			$c++;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		$this->template['list'] = $this->cObj->substituteSubpart($this->template['list'], 'LISTBODY', implode('', $tRows));

		return $this;
	}


	/**
	 * Returns a list header row.
	 *
	 * @return	obj   		this
	 * @see tslib_pibase::pi_list_header()
	 */
	public function pi_list_header() {
		$this->template['listHead'] = $this->cObj->getSubpart($this->template['all'], 'LISTHEAD');
		$markerArray = array(
			'###HEAD_SHELFMARK###'  => $this->pi_getLL('tx_archives_shelfmark'),
			'###HEAD_TITLE###'      => $this->pi_getLL('tx_archives_title'),
			'###HEAD_IMAGE###'      => $this->pi_getLL('tx_archives_image'),
			'###HEAD_YEAR###'       => $this->pi_getLL('tx_archives_year'),
			'###HEAD_CATEGORIES###' => $this->pi_getLL('tx_archives_categories'),
		);
		$this->template['listHead'] = $this->cObj->substituteMarkerArray($this->template['listHead'], $markerArray);
		$this->template['list']     = $this->cObj->substituteSubpart($this->template['list'], 'LISTHEAD', $this->template['listHead']);

		return $this;
	}


	/**
	 * Returns a list row. Get data from $this->internal['currentRow'];
	 *
	 * @param	integer		Row counting. Starts at 0 (zero). Used for alternating class values in the output rows.
	 * @return	string		HTML output, a table row with a class attribute set (alternative based on odd/even rows)
	 * @see tslib_pibase::pi_list_row()
	 */
	public function pi_list_row($c) {
		$this->template['listBody'] = $this->cObj->getSubpart($this->template['all'], 'LISTBODY');
		$markerArray = array(
			'###CLASS###' => ($c % 2) ? $this->pi_classParam('listrow-odd') : '',
		);
		foreach ($this->conf['listView.']['categories.'] as $category) {
				//  Labeling
			$markerArray['###LABEL_' . strtoupper($category) . '###'] = $this->pi_getLL('tx_archives_' . $category);
				//  get related categories
			$markerArray['###' . strtoupper($category) . '###']       = $this->getCategory2Item($this->internal['currentRow']['uid'], $category);
		}

		$fields4cObjCasting = array();
		foreach ($this->internal['currentRow'] as $crKey => $crVal) {
			$markerArray['###' . strtoupper($crKey) . '###'] = $crVal;
			if (!empty ($this->conf['listView.']['fields.'][$crKey])) {
				$fields4cObjCasting[] = $crKey;
			}
		}

			//  replace TS placeholder by field values
		$lConf = json_encode($this->conf['listView.']['fields.']);
		$lConf = $this->cObj->substituteMarkerArray($lConf, $markerArray);
		$lConf = json_decode($lConf, TRUE);
			//  cast fields as cObj
		foreach ($fields4cObjCasting as $fVal) {
			$name = $lConf[$fVal];
			$conf = $lConf[$fVal . '.'];
			$markerArray['###' .strtoupper($fVal)  . '###'] = $this->cObj->cObjGetSingle($name, $conf);
		}
		$content = $this->cObj->substituteMarkerArray($this->template['listBody'], $markerArray);

		return $content;
	}


	/**
	 * Returns a list of categories related to current record
	 *
	 * @param	integer		uid of current record
	 * @param   sring       name of current category
	 * @return	string		HTML output, a table row with a class attribute set (alternative based on odd/even rows)
	 */
	protected function getCategory2Item($uid, $category) {
		$select_fields = 'c.title';
		$from_table    = 'tx_archives_documents d
							LEFT JOIN tx_archives_documents_mm_tx_archives_' . $category . ' mm ON d.uid = mm.uid_local
							LEFT JOIN tx_archives_' . $category . ' c ON mm.uid_foreign = c.uid';
		$where_clause  = 'd.uid = ' . (int)$uid;
		$groupBy       = '';
		$orderBy       = 'mm.sorting';
		$limit         = '';
		$uidIndexField = '';
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit, $uidIndexField);

		$content = array();
		foreach ($rows as $row) {
			$content[] = $row['title'];
		}
		$content = implode(', ', $content);

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/archives/pi1/class.tx_archives_pi1.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/archives/pi1/class.tx_archives_pi1.php']);
}
?>