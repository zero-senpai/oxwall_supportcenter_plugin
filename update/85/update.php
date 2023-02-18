<?php

/* Copyright 2019-2020 JB Tech
* jbtech.online
* Update file for build 85 ver 4.0.0
*/

Updater::getLanguageService()->importPrefixFromZip(__DIR__ . DS . 'langs.zip', 'supportcenter');

//faq
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_faq`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_faq` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`question` text NOT NULL,
		`answer` text NOT NULL,
		`position` int(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

//added in build 50:
if(!OW::getConfig()->configExists("supportcenter", "faq_toggle")){
	OW::getConfig()->addConfig("supportcenter", "faq_toggle", "FALSE", "Turn the FAQ option on or off");
}
if(!OW::getConfig()->configExists("supportcenter", "auto_purge")){
	OW::getConfig()->addConfig("supportcenter", "auto_purge", "TRUE", "Auto-delete tickets after some time");
}
if(!OW::getConfig()->configExists("supportcenter", "purge_limit")){
	OW::getConfig()->addConfig("supportcenter", "purge_limit", "3Months", "Set the time limit to delete tickets");
}
if(!OW::getConfig()->configExists("supportcenter", "ticket_info_p")){
	OW::getConfig()->addConfig("supportcenter", "ticket_info_p", "Side", "Set position for Ticket Info bar");
}