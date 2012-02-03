#
# Table structure for table 'tx_archives_material'
#
CREATE TABLE tx_archives_material (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	title_lang_ol tinytext NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_archives_technique'
#
CREATE TABLE tx_archives_technique (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	title_lang_ol tinytext NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_archives_subject'
#
CREATE TABLE tx_archives_subject (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	title_lang_ol tinytext NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_archives_genre'
#
CREATE TABLE tx_archives_genre (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	title_lang_ol tinytext NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_archives_collection'
#
CREATE TABLE tx_archives_collection (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	title_lang_ol tinytext NOT NULL,
	setup int(11) DEFAULT '0' NOT NULL,
	provenance tinytext NOT NULL,
	focus text NOT NULL,
	comment text NOT NULL,
	period tinytext NOT NULL,
	collector tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_archives_collector'
#
CREATE TABLE tx_archives_collector (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	surname tinytext NOT NULL,
	firstname tinytext NOT NULL,
	dateofbirth int(11) DEFAULT '0' NOT NULL,
	career text NOT NULL,
	location tinytext NOT NULL,
	comment text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_archives_documents'
#
CREATE TABLE tx_archives_documents (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	shelfmark tinytext NOT NULL,
	title tinytext NOT NULL,
	year int(11) DEFAULT '0' NOT NULL,
	format tinytext NOT NULL,
	age int(11) DEFAULT '0' NOT NULL,
	gender int(11) DEFAULT '0' NOT NULL,
	material tinytext,
	technique tinytext,
	subject tinytext,
	genre tinytext,
	collection tinytext,
	image text,
	imagewidth mediumint(11) unsigned DEFAULT '0' NOT NULL,
	imageheight mediumint(8) unsigned DEFAULT '0' NOT NULL,
	imageorient tinyint(4) unsigned DEFAULT '0' NOT NULL,
	imagecaption text,
	imagecaption_position varchar(6) DEFAULT '' NOT NULL,
	imageseo text,
	imagecols tinyint(4) unsigned DEFAULT '0' NOT NULL,
	imageborder tinyint(4) unsigned DEFAULT '0' NOT NULL,
	image_link text,
	image_zoom tinyint(3) unsigned DEFAULT '0' NOT NULL,
	image_noRows tinyint(3) unsigned DEFAULT '0' NOT NULL,
	image_effects tinyint(3) unsigned DEFAULT '0' NOT NULL,
	image_compression tinyint(3) unsigned DEFAULT '0' NOT NULL,
	image_frames tinyint(3) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);