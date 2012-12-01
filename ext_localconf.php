<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


	//  uherrmann, 121117: add plugin
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_archives_pi1.php', '_pi1', 'list_type', 1);


t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_collection=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_collector=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_documents=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_genre=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_genredetails=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_material=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_materialdetails=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_subject=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_subjectdetails=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_technique=1');
t3lib_extMgm::addUserTSConfig('options.saveDocNew.tx_archives_techniquedetails=1');

t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_archives_collector", field "career"
	# ***************************************************************************************
RTE.config.tx_archives_collector.career {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');
?>