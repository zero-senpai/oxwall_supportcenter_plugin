<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'supportcenter');

if(!OW::getConfig()->configExists("supportcenter", "help_forum")){
	OW::getConfig()->addConfig("supportcenter", "help_forum", "FALSE", "Help Forum");
}

if(!OW::getConfig()->configExists("supportcenter", "help_forum_url")){
	OW::getConfig()->addConfig("supportcenter", "help_forum_url", "", "Help Forum URL");
}

if(!OW::getConfig()->configExists("supportcenter", "article_share")){
	OW::getConfig()->addConfig("supportcenter", "article_share", "TRUE", "Allow Article Sharing");
}

if(!OW::getConfig()->configExists("supportcenter", "recommend_art")){
	OW::getConfig()->addConfig("supportcenter", "recommend_art", "TRUE", "Recommend Articles");
}